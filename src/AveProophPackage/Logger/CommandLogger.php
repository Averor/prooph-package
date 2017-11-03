<?php

declare(strict_types=1);

namespace AveProophPackage\Logger;

use AveProophPackage\Domain\Command;

/**
 * Interface CommandLogger
 *
 * @package AveProophPackage\Logger
 * @author Averor <averor.dev@gmail.com>
 */
interface CommandLogger
{
    /**
     * @param Command $command
     * @return void
     */
    public function logCommand(Command $command) : void;
}
