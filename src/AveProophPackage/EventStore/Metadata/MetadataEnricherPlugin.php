<?php

declare(strict_types=1);

namespace AveProophPackage\EventStore\Metadata;

use ArrayIterator;
use Iterator;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Messaging\Message;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Metadata\MetadataEnricher;
use Prooph\EventStore\Plugin\Plugin as EventStorePlugin;
use Prooph\EventStore\Stream;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Plugin as MessageBusPlugin;

/**
 * Class MetadataEnricherPlugin
 * Based on the original
 *      Prooph\EventStore\Metadata\MetadataEnricherPlugin
 *      & Prooph\EventStoreBusBridge\CausationMetadataEnricher
 *
 * @package AveProophPackage\EventStore\Metadata
 * @author Averor <averor.dev@gmail.com>
 */
class MetadataEnricherPlugin implements EventStorePlugin, MessageBusPlugin
{
    /** @var MetadataEnricher */
    protected $metadataEnricher;

    /** @var Message */
    protected $currentCommand;

    /** @var array */
    protected $eventStoreListeners = [];

    /** @var array */
    protected $messageBusListeners = [];

    /**
     * @param MetadataEnricher $metadataEnricher
     */
    public function __construct(MetadataEnricher $metadataEnricher)
    {
        $this->metadataEnricher = $metadataEnricher;
    }

    /**
     * @param MessageBus $messageBus
     * @return void
     */
    public function attachToMessageBus(MessageBus $messageBus) : void
    {
        $this->messageBusListeners[] = $messageBus->attach(
            CommandBus::EVENT_DISPATCH,
            [$this, 'onCommandBusDispatchCommand'],
            CommandBus::PRIORITY_INVOKE_HANDLER + 1000
        );

        $this->messageBusListeners[] = $messageBus->attach(
            CommandBus::EVENT_FINALIZE,
            [$this, 'onCommandBusFinalizeCommand'],
            1000
        );
    }

    /**
     * @param ActionEventEmitterEventStore $eventStore
     * @return void
     */
    public function attachToEventStore(ActionEventEmitterEventStore $eventStore) : void
    {
        $this->eventStoreListeners[] = $eventStore->attach(
            ActionEventEmitterEventStore::EVENT_CREATE,
            [$this, 'onEventStoreCreateStream'],
            1000
        );

        $this->eventStoreListeners[] = $eventStore->attach(
            ActionEventEmitterEventStore::EVENT_APPEND_TO,
            [$this, 'onEventStoreAppendToStream'],
            1000
        );
    }

    /**
     * @param MessageBus $messageBus
     * @return void
     */
    public function detachFromMessageBus(MessageBus $messageBus) : void
    {
        foreach ($this->messageBusListeners as $listenerHandler) {
            $messageBus->detach($listenerHandler);
        }

        $this->messageBusListeners = [];
    }

    /**
     * @param ActionEventEmitterEventStore $eventStore
     * @return void
     */
    public function detachFromEventStore(ActionEventEmitterEventStore $eventStore) : void
    {
        foreach ($this->eventStoreListeners as $listenerHandler) {
            $eventStore->detach($listenerHandler);
        }

        $this->eventStoreListeners = [];
    }

    /**
     * @param ActionEvent $event
     * @return void
     */
    protected function onEventStoreCreateStream(ActionEvent $event) : void
    {
        $stream = $event->getParam('stream');

        if (! $stream instanceof Stream) {
            return;
        }

        $streamEvents = $stream->streamEvents();
        $streamEvents = $this->handleRecordedEvents($streamEvents);

        $event->setParam(
            'stream',
            new Stream(
                $stream->streamName(),
                $streamEvents,
                $stream->metadata()
            )
        );
    }

    /**
     * @param ActionEvent $event
     * @return void
     */
    protected function onEventStoreAppendToStream(ActionEvent $event) : void
    {
        $streamEvents = $event->getParam('streamEvents');

        if (! $streamEvents instanceof Iterator) {
            return;
        }

        $streamEvents = $this->handleRecordedEvents($streamEvents);

        $event->setParam(
            'streamEvents',
            $streamEvents
        );
    }

    /**
     * @param Iterator $events
     * @return Iterator
     */
    protected function handleRecordedEvents(Iterator $events) : Iterator
    {
        $enrichedEvents = [];

        foreach ($events as $event) {
            $enrichedEvents[] = $this->metadataEnricher->enrich($event);
        }

        return new ArrayIterator($enrichedEvents);
    }

    /**
     * @param ActionEvent $event
     * @return void
     */
    protected function onCommandBusDispatchCommand(ActionEvent $event) : void
    {
        $this->currentCommand = $event->getParam(CommandBus::EVENT_PARAM_MESSAGE);
    }

    /**
     * @param ActionEvent $event
     * @return void
     */
    protected function onCommandBusFinalizeCommand(ActionEvent $event) : void
    {
        $this->currentCommand = null;
    }
}
