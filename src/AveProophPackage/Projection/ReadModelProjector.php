<?php

declare(strict_types=1);

namespace AveProophPackage\Projection;

use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\Projection\ReadModel;
use Prooph\EventStore\Projection\Projector;
use Prooph\EventStore\Projection\ReadModelProjector as IReadModelProjector;

/**
 * Class ReadModelProjector
 *
 * @package AveProophPackage\Projection
 * @author Averor <averor.dev@gmail.com>
 */
abstract class ReadModelProjector
{
    /** @var string */
    protected static $projectorName = 'Undefined';

    /** @var IReadModelProjector */
    protected $projector;

    /**
     * @param ProjectionManager $projectionManager
     * @param ReadModel $readModel
     */
    public function __construct(ProjectionManager $projectionManager, ReadModel $readModel)
    {
        /** @var IReadModelProjector $projector */
        $this->projector = $projectionManager->createReadModelProjection(
            static::$projectorName,
            $readModel,
            [
                Projector::OPTION_PCNTL_DISPATCH => true
            ]
        );
    }

    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(
            [$this->projector, $name],
            $arguments
        );
    }

    /**
     * @param bool $keepRunning
     * @return void
     */
    public function run(bool $keepRunning = true) : void
    {
        $this->project()
            ->run($keepRunning);
    }

    /**
     * @return IReadModelProjector
     */
    abstract protected function project() : IReadModelProjector;
}
