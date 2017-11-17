<?php

declare(strict_types=1);

namespace AveProophPackage\MetadataEnricher;

use Assert\Assertion;
use Prooph\Common\Messaging\Message;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Metadata\MetadataEnricher;
use Prooph\EventStore\Plugin\Plugin as EventStorePlugin;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Plugin as MessageBusPlugin;

/**
 * Class MetadataEnricherAggregate
 * Based on the original Prooph\EventStore\Metadata\MetadataEnricherAggregate
 *
 * @package AveProophPackage\MetadataEnricher
 * @author Averor <averor.dev@gmail.com>
 */
class MetadataEnricherAggregate implements MetadataEnricher, EventStorePlugin, MessageBusPlugin
{
    /** @var MetadataEnricher[] */
    protected $metadataEnrichers;

    /**
     * @param MetadataEnricher[] $metadataEnrichers
     */
    public function __construct(array $metadataEnrichers)
    {
        Assertion::allIsInstanceOf($metadataEnrichers, MetadataEnricher::class);

        $this->metadataEnrichers = $metadataEnrichers;
    }

    /**
     * @param Message $message
     * @return Message
     */
    public function enrich(Message $message): Message
    {
        foreach ($this->metadataEnrichers as $metadataEnricher) {
            $message = $metadataEnricher->enrich($message);
        }

        return $message;
    }

    /**
     * @param MessageBus $messageBus
     * @return void
     */
    public function attachToMessageBus(MessageBus $messageBus) : void
    {
        foreach ($this->metadataEnrichers as $metadataEnricher) {
            if ($metadataEnricher instanceof MessageBusPlugin) {
                $metadataEnricher->attachToMessageBus($messageBus);
            }
        }
    }

    /**
     * @param ActionEventEmitterEventStore $eventStore
     * @return void
     */
    public function attachToEventStore(ActionEventEmitterEventStore $eventStore) : void
    {
        foreach ($this->metadataEnrichers as $metadataEnricher) {
            if ($metadataEnricher instanceof EventStorePlugin) {
                $metadataEnricher->attachToEventStore($eventStore);
            }
        }
    }

    /**
     * @param MessageBus $messageBus
     * @return void
     */
    public function detachFromMessageBus(MessageBus $messageBus) : void
    {
        foreach ($this->metadataEnrichers as $metadataEnricher) {
            if ($metadataEnricher instanceof MessageBusPlugin) {
                $metadataEnricher->detachFromMessageBus($messageBus);
            }
        }
    }

    /**
     * @param ActionEventEmitterEventStore $eventStore
     * @return void
     */
    public function detachFromEventStore(ActionEventEmitterEventStore $eventStore) : void
    {
        foreach ($this->metadataEnrichers as $metadataEnricher) {
            if ($metadataEnricher instanceof EventStorePlugin) {
                $metadataEnricher->detachFromEventStore($eventStore);
            }
        }
    }
}
