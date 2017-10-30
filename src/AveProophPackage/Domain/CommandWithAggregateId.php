<?php

declare(strict_types=1);

namespace AveProophPackage\Domain;

/**
 * Interface CommandWithAggregateId
 *
 * @package AveProophPackage\Domain
 * @author Averor <averor.dev@gmail.com>
 */
interface CommandWithAggregateId
{
    /**
     * @return string
     */
    public function aggregateId() : string;
}
