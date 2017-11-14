<?php

declare(strict_types=1);

namespace AveProophPackage\MetadataEnricher;

use AveProophPackage\Domain\Command;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Messaging\Message;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Plugin;

/**
 * Class CommandIssuerMetadataEnricher
 *
 * @package AveProophPackage\MetadataEnricher
 * @author Averor <averor.dev@gmail.com>
 */
abstract class CommandIssuerMetadataEnricher implements IssuerMetadataEnricher, Plugin
{
    /** @var Command */
    protected $currentCommand;

    /** @var array */
    protected $messageBusListeners = [];

    /**
     * Return the given message with added metadata.
     *
     * @param Message $command
     * @return Message
     */
    public function enrich(Message $command) : Message
    {
        $command = $command
            ->withAddedMetadata('issuer_id', $this->getUserId())
            ->withAddedMetadata('issuer_ip', $this->getUserIP());

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
                $this->currentCommand = $event->getParam(CommandBus::EVENT_PARAM_MESSAGE);
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
}
