<?php

declare(strict_types=1);

namespace AveProophPackage\Producer;

/**
 * Class AMQPEventProducer
 *
 * @package AveProophPackage\Producer
 * @author Averor <averor.dev@gmail.com>
 */
class AMQPEventProducer extends AMQPMessageProducer
{
    const QUEUE_NAME = 'events';
}
