<?php

declare(strict_types=1);

namespace AveProophPackage\Domain;

use Prooph\EventSourcing\AggregateRoot as BaseAggregateRoot;

/**
 * Class AggregateRoot
 *
 * @package AveProophPackage\Domain
 * @author Averor <averor.dev@gmail.com>
 */
abstract class AggregateRoot extends BaseAggregateRoot
{
    /**
     * @param DomainEvent $event
     */
    protected function apply(DomainEvent $event) : void
    {
        $eventClassName = (new \ReflectionClass($event))->getShortName();
        $method = 'when' . $eventClassName;

        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException(sprintf(
                "Method %s needed to handle %s event not found in %s aggregate root object",
                $method,
                $eventClassName,
                self::class
            ));
        }

        $this->$method($event);
    }
}
