<?php

declare(strict_types=1);

namespace AveProophPackage\EventBus;

use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\ListenerExceptionCollectionMode;
use Prooph\ServiceBus\Plugin\Router\EventRouter;

/**
 * Class EventBusFactory
 *
 * @package AveProophPackage\EventBus
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

        (new ListenerExceptionCollectionMode())
            ->attachToMessageBus($eventBus);

        return $eventBus;
    }
}
