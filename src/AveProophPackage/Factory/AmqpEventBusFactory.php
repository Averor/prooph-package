<?php

declare(strict_types=1);

namespace AveProophPackage\Factory;

use Assert\Assertion;
use AveProophPackage\Producer\AMQPEventProducer;
use AveProophPackage\Plugin\EventListenerExceptionHandler;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\Router\AsyncSwitchMessageRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
use Psr\Log\LoggerInterface;

/**
 * Class AMQPEventBusFactory
 *
 * @package AveProophPackage\Factory
 * @author Averor <averor.dev@gmail.com>
 */
class AMQPEventBusFactory
{
    /**
     * @param EventRouter $router
     * @param array $amqpConnectionDetails
     * @param LoggerInterface $logger
     * @return EventBus
     * @throws \Assert\AssertionFailedException
     */
    public static function create(
        EventRouter $router,
        array $amqpConnectionDetails,
        LoggerInterface $logger
    ) : EventBus {

        Assertion::keyExists($amqpConnectionDetails, 'host');
        Assertion::keyExists($amqpConnectionDetails, 'port');
        Assertion::keyExists($amqpConnectionDetails, 'user');
        Assertion::keyExists($amqpConnectionDetails, 'pass');

        $eventBus = new EventBus(
            new ProophActionEventEmitter()
        );

        $eventRouter = new AsyncSwitchMessageRouter(
            $router,
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

        (new EventListenerExceptionHandler($logger))
            ->attachToMessageBus($eventBus);

        return $eventBus;
    }
}
