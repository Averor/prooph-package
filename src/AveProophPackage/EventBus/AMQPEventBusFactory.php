<?php

declare(strict_types=1);

namespace AveProophPackage\EventBus;

use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\ServiceBus\Async\MessageProducer;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\ListenerExceptionCollectionMode;
use Prooph\ServiceBus\Plugin\Plugin;
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
     * @param array|null $plugins
     * @return EventBus
     */
    public static function create(array $routingMap, MessageProducer $producer, array $plugins = null) : EventBus
    {
        $plugins = $plugins ?: [];

        $eventBus = new EventBus(
            new ProophActionEventEmitter()
        );

        $eventRouter = new AsyncSwitchMessageRouter(
            $eventRouter = new EventRouter($routingMap),
            $producer
        );

        array_push($plugins, $eventRouter);
        array_push($plugins, new ListenerExceptionCollectionMode());

        /** @var Plugin $plugin */
        foreach ($plugins as $plugin) {
            $plugin->attachToMessageBus($eventBus);
        }

        return $eventBus;
    }
}
