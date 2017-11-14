<?php

declare(strict_types=1);

namespace AveProophPackage\Factory;

use AveProophPackage\EventStore\Metadata\MetadataEnricherAggregate;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;

/**
 * Class CommandBusFactory
 *
 * @package AveProophPackage\Factory
 * @author Averor <averor.dev@gmail.com>
 */
class CommandBusFactory
{
    /**
     * @param array $routingMap
     * @param MetadataEnricherAggregate|null $metadataEnricherAggregate
     * @return CommandBus
     */
    public static function create(array $routingMap, ?MetadataEnricherAggregate $metadataEnricherAggregate) : CommandBus
    {
        $commandBus = new CommandBus();

        (new CommandRouter($routingMap))
            ->attachToMessageBus($commandBus);

        if ($metadataEnricherAggregate) {
            $metadataEnricherAggregate->attachToMessageBus($commandBus);
        }

        return $commandBus;
    }
}
