<?php

declare(strict_types=1);

namespace AveProophPackage\Snapshot;

use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\SnapshotStore\SnapshotStore;
use Prooph\Snapshotter\SnapshotReadModel;
use Prooph\Snapshotter\StreamSnapshotProjection;

/**
 * Class Snapshotter
 *
 * @package AveProophPackage\Snapshot
 * @author Averor <averor.dev@gmail.com>
 */
abstract class Snapshotter
{
    /** @var string */
    protected static $projectorName = 'undefined';

    /** @var string */
    protected static $streamName = 'undefined';

    /** @var array */
    protected static $aggregateTypes = [];

    /** @var StreamSnapshotProjection */
    protected $projector;

    /**
     * @param ProjectionManager $projectionManager
     * @param AggregateRepository $repository
     * @param SnapshotStore $snapshotStore
     */
    public function __construct(
        ProjectionManager $projectionManager,
        AggregateRepository $repository,
        SnapshotStore $snapshotStore
    ) {
        /** @var StreamSnapshotProjection $projector */
        $this->projector = new StreamSnapshotProjection(
            $projectionManager->createReadModelProjection(
                static::$projectorName,
                new SnapshotReadModel(
                    $repository,
                    new AggregateTranslator(),
                    $snapshotStore,
                    static::$aggregateTypes
                ),
                [
                    \Prooph\EventStore\Projection\Projector::OPTION_PCNTL_DISPATCH => true
                ]
            ),
            static::$streamName
        );
    }

    /**
     * @param bool $keepRunning
     */
    public function run(bool $keepRunning = true)
    {
        ($this->projector)($keepRunning);
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
}
