<?php

declare(strict_types=1);

namespace AveProophPackage\Factory;

use Prooph\EventStoreBusBridge\CausationMetadataEnricher;
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
     * @param CausationMetadataEnricher $causationMetadataEnricher
     * @return CommandBus
     */
    public static function create(array $routingMap, CausationMetadataEnricher $causationMetadataEnricher) : CommandBus
    {
        $commandBus = new CommandBus();

        (new CommandRouter($routingMap))
            ->attachToMessageBus($commandBus);

        $causationMetadataEnricher->attachToMessageBus($commandBus);

        return $commandBus;
    }
}
