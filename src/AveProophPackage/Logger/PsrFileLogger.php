<?php

declare(strict_types=1);

namespace AveProophPackage\Logger;

use AveProophPackage\Domain\Command;
use AveProophPackage\Domain\CommandHandler;
use AveProophPackage\Domain\DomainEvent;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Class PsrFileLogger
 *
 * @package AveProophPackage\Logger
 * @author Averor <averor.dev@gmail.com>
 *
 * @todo Implement me :)
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
        // TODO: Implement logCommand() method.
    }

    /**
     * @param DomainEvent $event
     * @return void
     */
    public function logEvent(DomainEvent $event) : void
    {
        // TODO: Implement logEvent() method.
    }

    /**
     * @param Command $command
     * @param CommandHandler|null $handler
     * @param Throwable $exception
     * @return void
     */
    public function logFailedCommand(Command $command, ?CommandHandler $handler, Throwable $exception) : void
    {
        // TODO: Implement logFailedCommand() method.
    }

    /**
     * @param DomainEvent $event
     * @param Throwable $exception
     * @return void
     */
    public function logFailedEventListener(DomainEvent $event, Throwable $exception) : void
    {
        $this->logger->error(
            'EventListener Exception [' . get_class($exception) . ']' . $exception->getMessage(),
            ['exception' => $exception]
        );
    }
}
