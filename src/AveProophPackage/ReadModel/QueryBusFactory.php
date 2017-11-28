<?php

declare(strict_types=1);

namespace AveProophPackage\ReadModel;

use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\ServiceBus\Plugin\Plugin;
use Prooph\ServiceBus\Plugin\Router\QueryRouter;
use Prooph\ServiceBus\QueryBus;

/**
 * Class QueryBusFactory
 *
 * @package AveProophPackage\ReadModel
 * @author Averor <averor.dev@gmail.com>
 */
class QueryBusFactory
{
    /**
     * @param array $routingMap
     * @param array|null $plugins
     * @return QueryBus
     */
    public static function create(array $routingMap, array $plugins = null) : QueryBus
    {
        $plugins = $plugins ?: [];

        $queryBus = new QueryBus(
            new ProophActionEventEmitter()
        );

        array_push($plugins, new QueryRouter($routingMap));

        /** @var Plugin $plugin */
        foreach ($plugins as $plugin) {
            $plugin->attachToMessageBus($queryBus);
        }

        return $queryBus;
    }
}
