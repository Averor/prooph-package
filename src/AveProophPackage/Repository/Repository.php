<?php

declare(strict_types=1);

namespace AveProophPackage\Repository;

use AveProophPackage\Domain\AggregateRoot;
use AveProophPackage\Domain\Identifier;

/**
 * Interface Repository
 *
 * @package AveProophPackage\Repository
 * @author Averor <averor.dev@gmail.com>
 */
interface Repository
{
    /**
     * @param Identifier $id
     * @return AggregateRoot Aggregate root entity
     */
    public function get(Identifier $id) : AggregateRoot;

    /**
     * @param AggregateRoot $aggregateRoot Aggregate root entity
     * @return void
     */
    public function save(AggregateRoot $aggregateRoot) : void;
}
