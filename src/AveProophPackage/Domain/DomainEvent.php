<?php

declare(strict_types=1);

namespace AveProophPackage\Domain;

use Prooph\EventSourcing\AggregateChanged;

/**
 * Class DomainEvent
 *
 * @package AveProophPackage\Domain
 * @author Averor <averor.dev@gmail.com>
 */
abstract class DomainEvent extends AggregateChanged
{
}
