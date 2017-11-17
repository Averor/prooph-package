<?php

declare(strict_types=1);

namespace AveProophPackage\Consumer;

use DateTimeImmutable;
use DateTimeZone;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Prooph\Common\Messaging\DomainEvent;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\Common\Messaging\MessageDataAssertion;
use Prooph\EventStore\Exception\ConcurrencyException;
use Prooph\ServiceBus\EventBus;
use Psr\Log\LoggerInterface;

/**
 * Class AMQPConsumer
 *
 * @package AveProophPackage\Consumer
 * @author Averor <averor.dev@gmail.com>
 */
class AMQPConsumer
{
    /** @var AMQPStreamConnection */
    protected $connection;

    /** @var EventBus */
    protected $eventBus;

    /** @var LoggerInterface */
    protected $logger;

    /** @var resource */
    protected $outputReader;

    /**
     * @param AMQPStreamConnection $connection
     * @param EventBus $eventBus
     * @param LoggerInterface $logger
     */
    public function __construct(AMQPStreamConnection $connection, EventBus $eventBus, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->eventBus = $eventBus;
        $this->logger = $logger;
    }

    /**
     * @param string $queueName
     * @param resource $outputReader
     * @return void
     */
    public function run(string $queueName, $outputReader) : void
    {
        $this->outputReader = $outputReader;

        /** @var AMQPChannel $channel */
        $channel = $this->connection->channel();

        // Declare exchange to be sure it exists
        // This should be done somewhere else, in fact...
        $channel->exchange_declare(
            'app_messages',
            'fanout',
            false,
            false,
            false
        );

        // Declare queue to be sure it exists
        $channel->queue_declare(
            $queueName,
            false,
            true,
            false,
            false
        );

        $channel->queue_bind(
            $queueName,
            'app_messages'
        );

        $channel->basic_consume(
            $queueName,
            '',
            false,
            false,
            false,
            false,
            [$this, 'handle']
        );

        while(count($channel->callbacks)) {

            fwrite($this->outputReader, '[AMQPConsumer::INFO] Awaiting messages');

            $channel->wait();
        }

        $channel->close();

        $this->connection->close();
    }

    /**
     * @param AMQPMessage $message
     * @return void
     */
    public function handle(AMQPMessage $message) : void
    {
        try {

            fwrite($this->outputReader, '[AMQPConsumer::INFO] Message received');

            /** @var FQCNMessageFactory $messageFactory */
            $messageFactory = new FQCNMessageFactory();

            $messageData = json_decode($message->getBody(), true);

            if (!isset($messageData['created_at'])) {
                throw new \Exception("Missing field: 'created_at'");
            }

            $messageData['created_at'] = DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i:s.u',
                $messageData['created_at'],
                new DateTimeZone('UTC')
            );

            if (false === $messageData['created_at']) {
                throw new \Exception("Field: 'created_at' does not contain valid date time string");
            }

            MessageDataAssertion::assert($messageData);

            try {

                fwrite($this->outputReader, sprintf(
                    "[AMQPConsumer::INFO] Message validated - %s [%s]",
                    $messageData['message_name'],
                    $messageData['uuid']
                ));

                /** @var DomainEvent $event */
                $event = $messageFactory->createMessageFromArray(
                    $messageData['message_name'],
                    $messageData
                );

                $this->eventBus->dispatch($event);

                fwrite($this->outputReader, '[AMQPConsumer::OK] Message dispatched');

                $this->confirmMessage($message);

            } catch (\Throwable $e) {

                $originalException = $e;

                while ($e = $e->getPrevious()) {
                    if ($e instanceof ConcurrencyException) {
                        $this->rejectMessage(
                            $message,
                            $e->getMessage(),
                            true
                        );

                        return;
                    }
                }

                throw $originalException;
            }

        } catch (\Throwable $e) {

            $this->rejectMessage(
                $message,
                $e->getMessage(),
                false
            );

            $this->logger->error(
                'AMQPConsumer Exception: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }
    }

    /**
     * @param AMQPMessage $message
     * @param string|null $reason
     * @param bool $requeue
     * @return void
     */
    protected function rejectMessage(AMQPMessage $message, ?string $reason, bool $requeue) : void
    {
        fwrite($this->outputReader, sprintf(
            "[AMQPConsumer::ERROR] Message rejected [%s]. Reason: %s",
            ($requeue ? 'Will be requeued.' : 'Will not be requeued'),
            ($reason ?? '-')
        ));

        $message->delivery_info['channel']->basic_reject(
            $message->delivery_info['delivery_tag'],
            $requeue
        );
    }

    /**
     * @param AMQPMessage $message
     * @param null|string $reason
     */
    protected function confirmMessage(AMQPMessage $message, ?string $reason = null) : void
    {
        fwrite($this->outputReader,sprintf(
            "[AMQPConsumer::OK] Message confirmed%s",
            ($reason ? '. Reason: '.$reason : '')
        ));

        $message->delivery_info['channel']->basic_ack(
            $message->delivery_info['delivery_tag']
        );
    }
}
