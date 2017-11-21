<?php

declare(strict_types=1);

namespace AveProophPackage\EventStore;

use AveProophPackage\MetadataEnricher\MetadataEnricherAggregate;
use PDO;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Pdo\MySqlEventStore;
use Prooph\EventStore\Plugin\Plugin;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\ServiceBus\EventBus;

/**
 * Class MySqlEventStoreFactory
 *
 * @package AveProophPackage\EventStore
 * @author Averor <averor.dev@gmail.com>
 */
class MySqlEventStoreFactory
{
    /**
     * @param PDO $pdo
     * @param EventBus $eventBus
     * @param MetadataEnricherAggregate|null $metadataEnricherAggregate
     * @param array|null $plugins
     * @return EventStore
     */
    public static function create(
        PDO $pdo,
        EventBus $eventBus,
        ?MetadataEnricherAggregate $metadataEnricherAggregate,
        array $plugins = null
    ) : EventStore {

        $plugins = $plugins ?: [];

        $eventStore = new MySqlEventStore(
            new FQCNMessageFactory(),
            $pdo,
            new MysqlPersistenceStrategy()
        );

        $actionEventEmitterEventStore = new ActionEventEmitterEventStore(
            $eventStore,
            new ProophActionEventEmitter()
        );

        array_push($plugins, new EventPublisher($eventBus));

        if ($metadataEnricherAggregate) {
            array_push($plugins, $metadataEnricherAggregate);
        }

        /** @var Plugin $plugin */
        foreach ($plugins as $plugin) {
            $plugin->attachToEventStore($actionEventEmitterEventStore);
        }

        return $actionEventEmitterEventStore;
    }
}
