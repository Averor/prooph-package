<?php

declare(strict_types=1);

namespace AveProophPackage\Factory;

use AveProophPackage\EventStore\MysqlPersistenceStrategy;
use PDO;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Pdo\MySqlEventStore;
use Prooph\EventStoreBusBridge\CausationMetadataEnricher;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\ServiceBus\EventBus;

/**
 * Class MySqlEventStoreFactory
 *
 * @package AveProophPackage\Factory
 * @author Averor <averor.dev@gmail.com>
 */
class MySqlEventStoreFactory
{
    /**
     * @param PDO $pdo
     * @param EventBus $eventBus
     * @param CausationMetadataEnricher $causationMetadataEnricher
     * @return EventStore
     */
    public static function create(PDO $pdo, EventBus $eventBus, CausationMetadataEnricher $causationMetadataEnricher) : EventStore
    {
        $eventStore = new MySqlEventStore(
            new FQCNMessageFactory(),
            $pdo,
            new MysqlPersistenceStrategy()
        );

        $actionEventEmitterEventStore = new ActionEventEmitterEventStore(
            $eventStore,
            new ProophActionEventEmitter()
        );

        $eventPublisher = new EventPublisher($eventBus);
        $eventPublisher->attachToEventStore($actionEventEmitterEventStore);

        $causationMetadataEnricher->attachToEventStore($actionEventEmitterEventStore);

        return $actionEventEmitterEventStore;
    }
}
