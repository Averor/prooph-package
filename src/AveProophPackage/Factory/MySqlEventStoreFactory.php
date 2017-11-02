<?php

declare(strict_types=1);

namespace AveProophPackage\Factory;

use PDO;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Pdo\MySqlEventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy\MySqlSingleStreamStrategy;
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
     * @return EventStore
     */
    public static function create(PDO $pdo, EventBus $eventBus) : EventStore
    {
        $eventStore = new MySqlEventStore(
            new FQCNMessageFactory(),
            $pdo,
            new MySqlSingleStreamStrategy()
        );

        $actionEventEmitterEventStore = new ActionEventEmitterEventStore(
            $eventStore,
            new ProophActionEventEmitter()
        );

        $eventPublisher = new EventPublisher($eventBus);
        $eventPublisher->attachToEventStore($actionEventEmitterEventStore);

        (new CausationMetadataEnricher())
            ->attachToEventStore($actionEventEmitterEventStore);

        return $actionEventEmitterEventStore;
    }
}
