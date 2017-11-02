<?php

declare(strict_types=1);

namespace AveProophPackage\Producer;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageConverter;
use Prooph\Common\Messaging\MessageDataAssertion;
use Prooph\Common\Messaging\NoOpMessageConverter;
use Prooph\ServiceBus\Async\MessageProducer;
use Prooph\ServiceBus\Exception\RuntimeException;
use React\Promise\Deferred;

/**
 * Class AMQPMessageProducer
 *
 * @package AveProophPackage\Producer
 * @author Averor <averor.dev@gmail.com>
 */
abstract class AMQPMessageProducer implements MessageProducer
{
    const QUEUE_NAME = '_undefined_';

    /** @var AMQPStreamConnection */
    protected $connection;

    /** @var AMQPChannel */
    protected $channel;

    /** @var MessageConverter */
    protected $messageConverter;

    /**
     * @param AMQPStreamConnection $connection
     */
    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
        $this->messageConverter = new NoOpMessageConverter();

        /** @var AMQPChannel $channel */
        $this->channel = $this->connection->channel();

        // Declare queue to be sure it exists
        // (in other words - it's not necessary if we are sure that queue already exists)
        $this->channel->queue_declare(
            static::QUEUE_NAME,
            false,
            true,
            false,
            false
        );
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @inheritdoc
     */
    public function __invoke(Message $message, Deferred $deferred = null) : void
    {
        if (null !== $deferred) {
            throw new RuntimeException(__CLASS__ . ' cannot handle query messages which require future responses.');
        }

        $data = $this->arrayFromMessage($message);

        $this->channel->basic_publish(
            new AMQPMessage(
                json_encode($data),
                [
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
                ]
            ),
            '',
            static::QUEUE_NAME
        );
    }

    /**
     * @param Message $message
     * @return array
     */
    protected function arrayFromMessage(Message $message) : array
    {
        $messageData = $this->messageConverter->convertToArray($message);
        MessageDataAssertion::assert($messageData);
        $messageData['created_at'] = $message->createdAt()->format('Y-m-d\TH:i:s.u');

        return $messageData;
    }
}
