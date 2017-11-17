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
 * Class SnapshotProjectorFactory
 *
 * @package AveProophPackage\Snapshot
 * @author Averor <averor.dev@gmail.com>
 */
class SnapshotProjectorFactory
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
     * @param string $streamName
     * @param array $aggregateTypes
     * @param AggregateRepository $repository
     * @param SnapshotStore $snapshotStore
     * @return StreamSnapshotProjection
     */
    public function create(
        string $name,
        string $streamName,
        array $aggregateTypes,
        AggregateRepository $repository,
        SnapshotStore $snapshotStore
    ) : StreamSnapshotProjection {

        return new StreamSnapshotProjection(
            $this->projectionManager->createReadModelProjection(
                $name,
                new SnapshotReadModel(
                    $repository,
                    new AggregateTranslator(),
                    $snapshotStore,
                    $aggregateTypes
                ),
                [
                    \Prooph\EventStore\Projection\Projector::OPTION_PCNTL_DISPATCH => true
                ]
            ),
            $streamName
        );
    }
}
