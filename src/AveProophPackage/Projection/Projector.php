<?php

declare(strict_types=1);

namespace AveProophPackage\Projection;

use Prooph\EventStore\Projection\ReadModelProjector;

/**
 * Interface Projector
 *
 * @package AveProophPackage\Projection
 * @author Averor <averor.dev@gmail.com>
 */
interface Projector
{
    /**
     * @param ReadModelProjector $projector
     * @return ReadModelProjector
     */
    public function project(ReadModelProjector $projector) : ReadModelProjector;
}
