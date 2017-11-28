<?php

declare(strict_types=1);

namespace AveProophPackage\EventBus;

use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\ListenerExceptionCollectionMode;
use Prooph\ServiceBus\Plugin\Plugin;
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
     * @param array|null $plugins
     * @return EventBus
     */
    public static function create(array $routingMap, array $plugins = null) : EventBus
    {
        $plugins = $plugins ?: [];

        $eventBus = new EventBus(
            new ProophActionEventEmitter()
        );

        array_push($plugins, new EventRouter($routingMap));
        array_push($plugins, new ListenerExceptionCollectionMode());

        /** @var Plugin $plugin */
        foreach ($plugins as $plugin) {
            $plugin->attachToMessageBus($eventBus);
        }

        return $eventBus;
    }
}
