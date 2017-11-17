<?php

declare(strict_types=1);

namespace AveProophPackage\EventBus;

use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\ServiceBus\Async\MessageProducer;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\ListenerExceptionCollectionMode;
use Prooph\ServiceBus\Plugin\Router\AsyncSwitchMessageRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;

/**
 * Class AMQPEventBusFactory
 *
 * @package AveProophPackage\EventBus
 * @author Averor <averor.dev@gmail.com>
 */
class AMQPEventBusFactory
{
    /**
     * @param array $routingMap
     * @param MessageProducer $producer
     * @return EventBus
     * @throws \Assert\AssertionFailedException
     */
    public static function create(array $routingMap, MessageProducer $producer) : EventBus
    {
        $eventBus = new EventBus(
            new ProophActionEventEmitter()
        );

        $eventRouter = new AsyncSwitchMessageRouter(
            $eventRouter = new EventRouter($routingMap),
            $producer
        );
        $eventRouter->attachToMessageBus($eventBus);

        (new ListenerExceptionCollectionMode())
            ->attachToMessageBus($eventBus);

        return $eventBus;
    }
}
