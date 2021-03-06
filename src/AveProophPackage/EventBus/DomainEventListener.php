<?php

declare(strict_types=1);

namespace AveProophPackage\EventBus;

use AveProophPackage\Domain\DomainEvent;

/**
 * Class DomainEventListener
 *
 * @package AveProophPackage\EventBus
 * @author Averor <averor.dev@gmail.com>
 */
abstract class DomainEventListener
{
    /**
     * @param DomainEvent $event
     */
    public function __invoke(DomainEvent $event) : void
    {
        $eventClassName = (new \ReflectionClass($event))->getShortName();
        $method = 'on' . $eventClassName;

        if (method_exists($this, $method)) {
            $this->$method($event);
        }
    }
}
