<?php

declare(strict_types=1);

namespace AveProophPackage\Snapshot;

use PDO;
use Prooph\SnapshotStore\Pdo\PdoSnapshotStore;
use Prooph\SnapshotStore\SnapshotStore;

/**
 * Class MySqlSnapshotStoreFactory
 *
 * @package AveProophPackage\Snapshot
 * @author Averor <averor.dev@gmail.com>
 */
class MySqlSnapshotStoreFactory
{
    public static function create(PDO $pdo) : SnapshotStore
    {
        return new PdoSnapshotStore(
            $pdo,
            [], // map aggregate type to separate table. Tables must be created manually first!
            'snapshots'
        );
    }
}
