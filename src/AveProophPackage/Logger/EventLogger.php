<?php

declare(strict_types=1);

namespace AveProophPackage\Logger;

use AveProophPackage\Domain\DomainEvent;

/**
 * Interface EventLogger
 *
 * @package AveProophPackage\Logger
 * @author Averor <averor.dev@gmail.com>
 */
interface EventLogger
{
    /**
     * @param DomainEvent $event
     * @return void
     */
    public function logEvent(DomainEvent $event) : void;
}
