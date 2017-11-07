<?php

declare(strict_types=1);

namespace AveProophPackage\Domain;

use AveProophPackage\Repository\Repository;

/**
 * Class CommandHandler
 *
 * @package AveProophPackage\Domain
 * @author Averor <averor.dev@gmail.com>
 */
abstract class CommandHandler
{
    /** @var Repository */
    protected $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }
}
