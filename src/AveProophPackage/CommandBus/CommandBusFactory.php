<?php

declare(strict_types=1);

namespace AveProophPackage\CommandBus;

use AveProophPackage\MetadataEnricher\MetadataEnricherAggregate;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Plugin\Plugin;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;

/**
 * Class CommandBusFactory
 *
 * @package AveProophPackage\CommandBus
 * @author Averor <averor.dev@gmail.com>
 */
class CommandBusFactory
{
    /**
     * @param array $routingMap
     * @param MetadataEnricherAggregate|null $metadataEnricherAggregate
     * @param array|null $plugins
     * @return CommandBus
     */
    public static function create(
        array $routingMap,
        ?MetadataEnricherAggregate $metadataEnricherAggregate,
        array $plugins = null
    ) : CommandBus {

        $plugins = $plugins ?: [];

        $commandBus = new CommandBus();

        array_push($plugins, new CommandRouter($routingMap));

        if ($metadataEnricherAggregate) {
            array_push($plugins, $metadataEnricherAggregate);
        }

        /** @var Plugin $plugin */
        foreach ($plugins as $plugin) {
            $plugin->attachToMessageBus($commandBus);
        }

        return $commandBus;
    }
}
