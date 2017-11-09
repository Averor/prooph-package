<?php

declare(strict_types=1);

namespace AveProophPackage\EventStore\Metadata;

use Assert\Assertion;
use Prooph\Common\Messaging\Message;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Metadata\MetadataEnricher;
use Prooph\EventStore\Plugin\Plugin as EventStorePlugin;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Plugin as MessageBusPlugin;

/**
 * Class MetadataEnricherAggregate
 * It's an extension of original Prooph\EventStore\Metadata\MetadataEnricherAggregate
 * that is, for some unknown reason, final...
 *
 * @package AveProophPackage\EventStore\Metadata
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

    public function enrich(Message $message): Message
    {
        foreach ($this->metadataEnrichers as $metadataEnricher) {
            $message = $metadataEnricher->enrich($message);
        }

        return $message;
    }

    public function attachToMessageBus(MessageBus $messageBus) : void
    {
        foreach ($this->metadataEnrichers as $metadataEnricher) {
            if ($metadataEnricher instanceof MessageBusPlugin) {
                $metadataEnricher->attachToMessageBus($messageBus);
            }
        }
    }

    public function attachToEventStore(ActionEventEmitterEventStore $eventStore) : void
    {
        foreach ($this->metadataEnrichers as $metadataEnricher) {
            if ($metadataEnricher instanceof EventStorePlugin) {
                $metadataEnricher->attachToEventStore($eventStore);
            }
        }
    }

    public function detachFromMessageBus(MessageBus $messageBus) : void
    {
        foreach ($this->metadataEnrichers as $metadataEnricher) {
            if ($metadataEnricher instanceof MessageBusPlugin) {
                $metadataEnricher->detachFromMessageBus($messageBus);
            }
        }
    }

    public function detachFromEventStore(ActionEventEmitterEventStore $eventStore) : void
    {
        foreach ($this->metadataEnrichers as $metadataEnricher) {
            if ($metadataEnricher instanceof EventStorePlugin) {
                $metadataEnricher->detachFromEventStore($eventStore);
            }
        }
    }
}
