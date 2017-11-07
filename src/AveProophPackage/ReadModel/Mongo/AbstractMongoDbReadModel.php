<?php

declare(strict_types=1);

namespace AveProophPackage\ReadModel\Mongo;

use MongoDB\Database;
use MongoDB\Model\CollectionInfo;
use Prooph\EventStore\Projection\AbstractReadModel as BaseAbstractReadModel;

/**
 * Class AbstractMongoDbReadModel
 *
 * @package AveProophPackage\ReadModel\Mongo
 * @author Averor <averor.dev@gmail.com>
 */
abstract class AbstractMongoDbReadModel extends BaseAbstractReadModel
{
    const COLLECTION_NAME = 'Undefined';

    /** @var Database */
    protected $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @return void
     */
    public function init() : void
    {
        $this->db
            ->createCollection(static::COLLECTION_NAME);
    }

    /**
     * @return bool
     */
    public function isInitialized() : bool
    {
        $collections = $this->db
            ->listCollections();

        /** @var CollectionInfo $collectionInfo */
        foreach ($collections as $collectionInfo) {
            if (static::COLLECTION_NAME === $collectionInfo->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    public function reset() : void
    {
        $this->delete();
        $this->init();
    }

    /**
     * @return void
     */
    public function delete() : void
    {
        $this->db
            ->dropCollection(static::COLLECTION_NAME);
    }

    /**
     * @param array $data
     * @return void
     */
    protected function insert(array $data): void
    {
        $this->db
            ->selectCollection(static::COLLECTION_NAME)
            ->insertOne($data);
    }
}
