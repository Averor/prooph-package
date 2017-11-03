<?php

declare(strict_types=1);

namespace AveProophPackage\Logger;

use AveProophPackage\Domain\DomainEvent;
use Throwable;

/**
 * Interface FailedEventListenerLogger
 *
 * @package AveProophPackage\Logger
 * @author Averor <averor.dev@gmail.com>
 */
interface FailedEventListenerLogger
{
    /**
     * @param DomainEvent $event
     * @param Throwable $exception
     * @return void
     */
    public function logFailedEventListener(DomainEvent $event, Throwable $exception) : void;
}
