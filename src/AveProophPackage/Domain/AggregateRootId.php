<?php

declare(strict_types=1);

namespace AveProophPackage\Domain;

use Ramsey\Uuid\Uuid;

/**
 * Class AggregateRootId
 *
 * @package AveProophPackage\Domain
 * @author Averor <averor.dev@gmail.com>
 */
class AggregateRootId implements Identifier
{
    /** @var string */
    protected $uuid;

    /**
     * @param string|null $string
     */
    public function __construct($string = null)
    {
        switch (true) {
            case ($string && !empty($string)):
                $this->uuid = Uuid::fromString($string)->toString();
                break;
            case (is_string($string) && empty($string)):
                $this->uuid = '';
                break;
            default:
                $this->uuid = Uuid::uuid4()->toString();
                break;
        }
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function toString() : string
    {
        return $this->uuid;
    }

    /**
     * @return Identifier|AggregateRootId
     */
    public static function create() : Identifier
    {
        return new static(null);
    }

    /**
     * @param string $string
     * @return Identifier|AggregateRootId
     */
    public static function fromString(string $string) : Identifier
    {
        return new static($string);
    }

    /**
     * @param string $string
     * @return bool
     */
    public static function isValid(string $string) : bool
    {
        return Uuid::isValid($string);
    }
}
