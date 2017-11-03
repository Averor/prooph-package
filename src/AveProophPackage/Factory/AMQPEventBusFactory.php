<?php

declare(strict_types=1);

namespace AveProophPackage\Factory;

use Assert\Assertion;
use AveProophPackage\Producer\AMQPEventProducer;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\Router\AsyncSwitchMessageRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;

/**
 * Class AMQPEventBusFactory
 *
 * @package AveProophPackage\Factory
 * @author Averor <averor.dev@gmail.com>
 */
class AMQPEventBusFactory
{
    /**
     * @param array $routingMap
     * @param array $amqpConnectionDetails
     * @return EventBus
     * @throws \Assert\AssertionFailedException
     */
    public static function create(array $routingMap, array $amqpConnectionDetails) : EventBus
    {
        Assertion::keyExists($amqpConnectionDetails, 'host');
        Assertion::keyExists($amqpConnectionDetails, 'port');
        Assertion::keyExists($amqpConnectionDetails, 'user');
        Assertion::keyExists($amqpConnectionDetails, 'pass');

        $eventBus = new EventBus(
            new ProophActionEventEmitter()
        );

        $eventRouter = new AsyncSwitchMessageRouter(
            $eventRouter = new EventRouter($routingMap),
            new AMQPEventProducer(
                new AMQPStreamConnection(
                    $amqpConnectionDetails['host'],
                    $amqpConnectionDetails['port'],
                    $amqpConnectionDetails['user'],
                    $amqpConnectionDetails['pass']
                )
            )
        );
        $eventRouter->attachToMessageBus($eventBus);

        return $eventBus;
    }
}
