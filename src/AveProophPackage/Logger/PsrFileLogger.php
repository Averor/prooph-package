<?php

declare(strict_types=1);

namespace AveProophPackage\Logger;

use AveProophPackage\Domain\Command;
use AveProophPackage\Domain\DomainEvent;
use Prooph\ServiceBus\Exception\EventListenerException;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Class PsrFileLogger
 *
 * @package AveProophPackage\Logger
 * @author Averor <averor.dev@gmail.com>
 */
class PsrFileLogger implements CommandLogger, EventLogger, FailedCommandLogger, FailedEventListenerLogger
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Command $command
     * @return void
     */
    public function logCommand(Command $command) : void
    {
        $this->logger->info(
            sprintf(
                "Command %s [%s] for aggregate [%s] dispatched",
                $command->messageName(),
                $command->uuid()->toString(),
                $command->aggregateId()
            ),
            [
                'payload' => json_encode($command->payload())
            ]
        );
    }

    /**
     * @param DomainEvent $event
     * @return void
     */
    public function logEvent(DomainEvent $event) : void
    {
        $this->logger->info(
            sprintf(
                "Event %s [%s] for aggregate [%s] dispatched",
                $event->messageName(),
                $event->uuid()->toString(),
                $event->aggregateId()
            ),
            [
                'payload' => json_encode($event->payload())
            ]
        );
    }

    /**
     * @param Command $command
     * @param string $handler
     * @param Throwable $exception
     * @return void
     */
    public function logFailedCommand(Command $command, string $handler, Throwable $exception) : void
    {
        $this->logger->error(
            sprintf(
                "Command %s [%s] for aggregate [%s] failed in handler %s with exception [%s] %s",
                $command->messageName(),
                $command->uuid()->toString(),
                $command->aggregateId(),
                $handler,
                get_class($exception),
                $exception->getMessage()
            ),
            [
                'payload' => json_encode($command->payload()),
                'exception' => $exception
            ]
        );
    }

    /**
     * @param DomainEvent $event
     * @param Throwable $exception
     * @return void
     */
    public function logFailedEventListener(DomainEvent $event, Throwable $exception) : void
    {
        if ($exception instanceof EventListenerException) {
            /** @var Throwable $ex */
            foreach ($exception->listenerExceptions() as $ex) {
                $this->logger->error(
                    'EventListener Exception [' . get_class($ex) . '] ' . $ex->getMessage(),
                    ['exception' => $ex]
                );
            }
        } else {
            $this->logger->error(
                'EventListener Exception [' . get_class($exception) . '] ' . $exception->getMessage(),
                ['exception' => $exception]
            );
        }
    }
}
