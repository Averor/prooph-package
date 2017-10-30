<?php

declare(strict_types=1);

namespace AveProophPackage\Domain;

/**
 * Interface Identifier
 *
 * @package AveProophPackage\Domain
 * @author Averor <averor.dev@gmail.com>
 */
interface Identifier
{
    /**
     * @return string
     */
    public function __toString() : string;

    /**
     * @return string
     */
    public function toString() : string;

    /**
     * @return Identifier
     */
    public static function create() : Identifier;

    /**
     * @param string $string
     * @return Identifier
     */
    public static function fromString(string $string) : Identifier;

    /**
     * @param string $string
     * @return bool
     */
    public static function isValid(string $string) : bool;
}
