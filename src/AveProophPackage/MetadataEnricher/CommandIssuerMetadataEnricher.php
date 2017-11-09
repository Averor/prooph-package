<?php

declare(strict_types=1);

namespace AveProophPackage\MetadataEnricher;

use Prooph\Common\Messaging\Message;
use Prooph\EventStore\Plugin\Plugin as EventStorePlugin;
use Prooph\ServiceBus\Plugin\Plugin as MessageBusPlugin;

/**
 * Class CommandIssuerMetadataEnricher
 *
 * @package AveProophPackage\MetadataEnricher
 * @author Averor <averor.dev@gmail.com>
 */
abstract class CommandIssuerMetadataEnricher implements IssuerMetadataEnricher, EventStorePlugin, MessageBusPlugin
{
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
}
