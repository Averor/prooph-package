<?php

declare(strict_types=1);

namespace AveProophPackage\Factory;

use PDO;
use Prooph\SnapshotStore\Pdo\PdoSnapshotStore;
use Prooph\SnapshotStore\SnapshotStore;

/**
 * Class MySqlSnapshotStoreFactory
 *
 * @package AveProophPackage\Factory
 * @author Averor <averor.dev@gmail.com>
 */
class MySqlSnapshotStoreFactory
{
    public static function create(PDO $pdo) : SnapshotStore
    {
        return new PdoSnapshotStore(
            $pdo
        );
    }
}
