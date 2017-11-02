<?php

declare(strict_types=1);

namespace AveProophPackage\Factory;

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
     * @return CommandBus
     */
    public static function create(array $routingMap) : CommandBus
    {
        $commandBus = new CommandBus();
        $commandRouter = new CommandRouter(
            $routingMap
        );
        $commandRouter->attachToMessageBus($commandBus);

        return $commandBus;
    }
}
