<?php

declare(strict_types=1);

namespace AveProophPackage\MetadataEnricher;

use Prooph\EventStore\Metadata\MetadataEnricher;

/**
 * Interface IssuerMetadataEnricher
 *
 * @package AveProophPackage\MetadataEnricher
 * @author Averor <averor.dev@gmail.com>
 */
interface IssuerMetadataEnricher extends MetadataEnricher
{
    /**
     * Returns message issuer unique id or name
     * @return string
     */
    public function getUserId() : string;

    /**
     * Returns message issuer IP
     * @return string
     */
    public function getUserIP() : string;
}
