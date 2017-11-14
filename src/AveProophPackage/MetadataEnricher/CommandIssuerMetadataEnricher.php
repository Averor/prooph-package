<?php

declare(strict_types=1);

namespace AveProophPackage\MetadataEnricher;

use ArrayIterator;
use AveProophPackage\Domain\Command;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Messaging\Message;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Plugin\Plugin as EventStorePlugin;
use Prooph\EventStore\Stream;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Plugin as MessageBusPlugin;

/**
 * Class CommandIssuerMetadataEnricher
 *
 * @package AveProophPackage\MetadataEnricher
 * @author Averor <averor.dev@gmail.com>
 */
abstract class CommandIssuerMetadataEnricher implements IssuerMetadataEnricher, EventStorePlugin, MessageBusPlugin
{
    /** @var Command */
    protected $currentCommand;

    /** @var array */
    protected $messageBusListeners = [];

    /** @var array */
    protected $eventStoreListeners = [];

    /**
     * Return the given message with added metadata.
     *
     * @param Message $command
     * @return Message
     */
    public function enrich(Message $command) : Message
    {
        $command = $command
            ->withAddedMetadata('_issuer_id', $this->getUserId())
            ->withAddedMetadata('_issuer_ip', $this->getUserIP());

        return $command;
    }

    /**
     * @param MessageBus $messageBus
     */
    public function attachToMessageBus(MessageBus $messageBus) : void
    {
        $this->messageBusListeners[] = $messageBus->attach(
            CommandBus::EVENT_DISPATCH,
            function (ActionEvent $event): void {

                $this->currentCommand = $this->enrich(
                    $event->getParam(CommandBus::EVENT_PARAM_MESSAGE)
                );

                $event->setParam(
                    CommandBus::EVENT_PARAM_MESSAGE,
                    $this->currentCommand
                );
            },
            CommandBus::PRIORITY_INITIALIZE + 1000
        );

        $this->messageBusListeners[] = $messageBus->attach(
            CommandBus::EVENT_FINALIZE,
            function (ActionEvent $event): void {
                $this->currentCommand = null;
            },
            1000
        );
    }

    /**
     * @param MessageBus $messageBus
     */
    public function detachFromMessageBus(MessageBus $messageBus) : void
    {
        foreach ($this->messageBusListeners as $listenerHandler) {
            $messageBus->detach($listenerHandler);
        }

        $this->messageBusListeners = [];
    }

    public function attachToEventStore(ActionEventEmitterEventStore $eventStore): void
    {
        $this->eventStoreListeners[] = $eventStore->attach(
            ActionEventEmitterEventStore::EVENT_APPEND_TO,
            function (ActionEvent $event): void {
                if (! $this->currentCommand instanceof Message) {
                    return;
                }

                $recordedEvents = $event->getParam('streamEvents');

                $enrichedRecordedEvents = [];

                foreach ($recordedEvents as $recordedEvent) {
                    $enrichedRecordedEvents[] = $this->enrich($recordedEvent);
                }

                $event->setParam('streamEvents', new ArrayIterator($enrichedRecordedEvents));
            },
            1000
        );

        $this->eventStoreListeners[] = $eventStore->attach(
            ActionEventEmitterEventStore::EVENT_CREATE,
            function (ActionEvent $event): void {
                if (! $this->currentCommand instanceof Message) {
                    return;
                }

                $stream = $event->getParam('stream');
                $recordedEvents = $stream->streamEvents();

                $enrichedRecordedEvents = [];

                foreach ($recordedEvents as $recordedEvent) {
                    $enrichedRecordedEvents[] = $this->enrich($recordedEvent);
                }

                $stream = new Stream(
                    $stream->streamName(),
                    new ArrayIterator($enrichedRecordedEvents),
                    $stream->metadata()
                );

                $event->setParam('stream', $stream);
            },
            1000
        );
    }

    public function detachFromEventStore(ActionEventEmitterEventStore $eventStore): void
    {
        foreach ($this->eventStoreListeners as $listenerHandler) {
            $eventStore->detach($listenerHandler);
        }

        $this->eventStoreListeners = [];
    }
}
