<?php

declare(strict_types=1);

namespace AveProophPackage\ReadModel;

use React\Promise\Deferred;

/**
 * Class QueryHandler
 *
 * @package AveProophPackage\ReadModel
 * @author Averor <averor.dev@gmail.com>
 */
abstract class QueryHandler
{
    /**
     * @param Query $query
     * @param Deferred $deferred
     */
    public function __invoke(Query $query, Deferred $deferred) : void
    {
        $className = (new \ReflectionClass($query))->getShortName();
        $method = 'handle' . $className;

        // @todo Checking for method is disabled, because as handler is registered for query it must define such method
        // if (method_exists($this, $method)) {
            $this->$method($query, $deferred);
        // }
    }
}
