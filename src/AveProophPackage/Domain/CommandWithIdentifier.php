<?php

declare(strict_types=1);

namespace AveProophPackage\Domain;

/**
 * Interface CommandWithIdentifier
 *
 * @package AveProophPackage\Domain
 * @author Averor <averor.dev@gmail.com>
 */
interface CommandWithIdentifier
{
    /**
     * @return string
     */
    public function aggregateId() : string;
}
