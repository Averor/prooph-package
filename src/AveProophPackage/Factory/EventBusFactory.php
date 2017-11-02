<?php

declare(strict_types=1);

namespace AveProophPackage\Factory;

use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\Router\EventRouter;

/**
 * Class EventBusFactory
 *
 * @package AveProophPackage\Factory
 * @author Averor <averor.dev@gmail.com>
 */
class EventBusFactory
{
    /**
     * @param array $routingMap
     * @return EventBus
     */
    public static function create(array $routingMap) : EventBus
    {
        $eventBus = new EventBus(
            new ProophActionEventEmitter()
        );
        $eventRouter = new EventRouter($routingMap);
        $eventRouter->attachToMessageBus($eventBus);

        return $eventBus;
    }
}
