<?php

declare(strict_types=1);

namespace AveProophPackage\Repository;

use ArrayIterator;
use AveProophPackage\Domain\Identifier;
use Prooph\EventSourcing\Aggregate\AggregateTranslator;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\AggregateRoot;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Prooph\SnapshotStore\SnapshotStore;

/**
 * Class EventSourcedRepository
 *
 * @package AveProophPackage\Repository
 * @author Averor <averor.dev@gmail.com>
 */
class EventSourcedRepository extends AggregateRepository
{
    /**
     * @param EventStore $eventStore
     * @param string $aggregateType
     * @param AggregateTranslator $aggregateTranslator
     * @param SnapshotStore|null $snapshotStore
     * @param string $streamName
     */
    public function __construct(
        EventStore $eventStore,
        string $aggregateType,
        AggregateTranslator $aggregateTranslator,
        ?SnapshotStore $snapshotStore,
        string $streamName
    ) {

        $streamName = new StreamName($streamName);

        if (!$eventStore->hasStream($streamName)) {
            $eventStore->create(
                new Stream(
                    $streamName,
                    new ArrayIterator()
                )
            );
        }

        parent::__construct($eventStore,
            AggregateType::fromAggregateRootClass($aggregateType),
            $aggregateTranslator,
            $snapshotStore,
            $streamName,
            false
        );
    }

    /**
     * @inheritdoc
     */
    public function get(Identifier $id) : AggregateRoot
    {
        /** @var AggregateRoot $ar */
        $ar = $this->getAggregateRoot($id->toString());

        if (!$ar) {
            throw new AggregateRootNotFoundException(sprintf(
                "Aggregate root with id '%s' not found",
                $id
            ));
        }

        return $ar;
    }

    /**
     * @inheritdoc
     */
    public function save(AggregateRoot $aggregateRoot) : void
    {
        $this->saveAggregateRoot($aggregateRoot);
    }
}
