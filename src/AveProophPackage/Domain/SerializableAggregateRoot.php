<?php

declare(strict_types=1);

namespace AveProophPackage\Domain;

use Prooph\EventSourcing\AggregateRoot;

/**
 * Interface SerializableAggregateRoot
 *
 * @package AveProophPackage\Domain
 * @author Averor <averor.dev@gmail.com>
 */
interface SerializableAggregateRoot
{
    /**
     * @return array
     */
    public function serialize() : array;

    /**
     * @param array $data
     * @return AggregateRoot
     */
    public static function deserialize(array $data) : AggregateRoot;
}
