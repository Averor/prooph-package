<?php

declare(strict_types=1);

namespace AveProophPackage\ReadModel\Mongo;

use AveProophPackage\ReadModel\QueryHandler;
use MongoDB\Database;

/**
 * Class AbstractMongoQueryHandler
 *
 * @package AveProophPackage\ReadModel\Mongo
 * @author Averor <averor.dev@gmail.com>
 */
abstract class AbstractMongoQueryHandler extends QueryHandler
{
    /** @var Database */
    protected $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }
}
