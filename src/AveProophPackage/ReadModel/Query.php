<?php

declare(strict_types=1);

namespace AveProophPackage\ReadModel;

use Prooph\Common\Messaging\PayloadTrait;
use Prooph\Common\Messaging\Query as BaseQuery;

/**
 * Class Query
 *
 * @package AveProophPackage\ReadModel
 * @author Averor <averor.dev@gmail.com>
 */
abstract class Query extends BaseQuery
{
    use PayloadTrait;
}
