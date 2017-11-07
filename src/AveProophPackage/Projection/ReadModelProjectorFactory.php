<?php

declare(strict_types=1);

namespace AveProophPackage\Projection;

use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\Projection\ReadModel;
use Prooph\EventStore\Projection\ReadModelProjector;

/**
 * Class ReadModelProjectorFactory
 *
 * @package AveProophPackage\Projection
 * @author Averor <averor.dev@gmail.com>
 */
class ReadModelProjectorFactory
{
    /** @var ProjectionManager */
    protected $projectionManager;

    /**
     * @param ProjectionManager $projectionManager
     */
    public function __construct(ProjectionManager $projectionManager)
    {
        $this->projectionManager = $projectionManager;
    }

    /**
     * @param string $name
     * @param Projector $projector
     * @param ReadModel $readModel
     * @return ReadModelProjector
     */
    public function create(string $name, Projector $projector, ReadModel $readModel) : ReadModelProjector
    {
        return $projector->project(
            $this->projectionManager->createReadModelProjection(
                $name,
                $readModel,
                [
                    \Prooph\EventStore\Projection\Projector::OPTION_PCNTL_DISPATCH => true
                ]
            )
        );
    }
}
