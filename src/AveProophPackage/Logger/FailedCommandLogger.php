<?php

declare(strict_types=1);

namespace AveProophPackage\Logger;

use AveProophPackage\Domain\Command;
use AveProophPackage\Domain\CommandHandler;
use Throwable;

/**
 * Interface FailedCommandLogger
 *
 * @package AveProophPackage\Logger
 * @author Averor <averor.dev@gmail.com>
 */
interface FailedCommandLogger
{
    /**
     * @param Command $command
     * @param string $handler
     * @param Throwable $exception
     * @return void
     */
    public function logFailedCommand(Command $command, string $handler, Throwable $exception) : void;
}
