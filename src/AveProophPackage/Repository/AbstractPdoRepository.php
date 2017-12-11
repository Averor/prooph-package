<?php

declare(strict_types=1);

namespace AveProophPackage\Repository;

use AveProophPackage\Domain\AggregateRoot;
use AveProophPackage\Domain\Identifier;
use AveProophPackage\Domain\SerializableAggregateRoot;
use PDO;
use Prooph\Common\Messaging\Message;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\ServiceBus\EventBus;

/**
 * Class AbstractPdoRepository
 *
 * To be used within non eventsourced system
 *
 * @package AveProophPackage\Repository
 * @author Averor <averor.dev@gmail.com>
 */
abstract class AbstractPdoRepository
{
    /** @var string */
    protected static $aggregateFqcn = 'undefined';

    /** @var PDO */
    protected $pdo;

    /** @var EventBus */
    protected $eventBus;

    /** @var AggregateType */
    protected $aggregateType;

    /** @var AggregateTranslator */
    protected $aggregateTranslator;

    /**
     * @param PDO $pdo
     * @param EventBus $eventBus
     */
    public function __construct(PDO $pdo, EventBus $eventBus)
    {
        $this->pdo = $pdo;
        $this->eventBus = $eventBus;
        $this->aggregateType = AggregateType::fromAggregateRootClass(static::$aggregateFqcn);
        $this->aggregateTranslator = new AggregateTranslator();
    }

    /**
     * @inheritdoc
     */
    public function get(Identifier $id) : AggregateRoot
    {
        /** @var array $data */
        $data = $this->doGet($id->toString());

        if (!$data) {
            throw new AggregateRootNotFoundException(sprintf(
                "Aggregate root with id '%s' not found",
                $id
            ));
        }

        /** @var SerializableAggregateRoot $ar */
        $ar = static::$aggregateFqcn;

        return $ar::deserialize($data);
    }

    /**
     * @param AggregateRoot|SerializableAggregateRoot $aggregateRoot
     */
    public function save(AggregateRoot $aggregateRoot) : void
    {
        $this->aggregateType->assert($aggregateRoot);

        /** @var Message[] $domainEvents */
        $domainEvents = $this->aggregateTranslator->extractPendingStreamEvents(
            $aggregateRoot
        );

        /** @var string $aggregateId */
        $aggregateId = $this->aggregateTranslator->extractAggregateId(
            $aggregateRoot
        );

        $enrichedEvents = [];

        /** @var Message $event */
        foreach ($domainEvents as $event) {
            $enrichedEvents[] = $this->enrichEventMetadata(
                $event,
                $aggregateId
            );
        }

        if (!count($enrichedEvents)) {
            return;
        }

        $this->doSave($aggregateRoot->serialize());

        foreach ($enrichedEvents as $event) {
            $this->eventBus->dispatch($event);
        }
    }

    /**
     * Add aggregate_id and aggregate_type as metadata to $domainEvent
     * Override this method in an extending repository to add more or different metadata.
     *
     * @see \Prooph\EventSourcing\Aggregate\AggregateRepository::enrichEventMetadata
     *
     * @param Message $domainEvent
     * @param string $aggregateId
     * @return Message
     */
    protected function enrichEventMetadata(Message $domainEvent, string $aggregateId) : Message
    {
        $domainEvent = $domainEvent->withAddedMetadata('_aggregate_id', $aggregateId);
        $domainEvent = $domainEvent->withAddedMetadata('_aggregate_type', $this->aggregateType->toString());

        return $domainEvent;
    }

    /**
     * @param string $id
     * @return array|null
     */
    abstract protected function doGet(string $id) : ?array;

    /**
     * @param array $data
     * @return void
     */
    abstract protected function doSave(array $data) : void;
}
