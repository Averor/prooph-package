<?php

declare(strict_types=1);

namespace AveProophPackage\Domain;

use Prooph\EventSourcing\AggregateRoot as BaseAggregateRoot;

/**
 * Class AggregateRoot
 *
 * @package AveProophPackage\Domain
 * @author Averor <averor.dev@gmail.com>
 *
 * @todo Implement some common magic, like generic apply* method
 */
abstract class AggregateRoot extends BaseAggregateRoot
{

}
