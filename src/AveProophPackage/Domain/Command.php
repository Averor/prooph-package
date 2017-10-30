<?php

declare(strict_types=1);

namespace AveProophPackage\Domain;

use Prooph\Common\Messaging\Command as BaseCommand;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

/**
 * Class Command
 *
 * @package AveProophPackage\Domain
 * @author Averor <averor.dev@gmail.com>
 */
abstract class Command extends BaseCommand implements CommandWithAggregateId, PayloadConstructable
{
    use PayloadTrait;
}
