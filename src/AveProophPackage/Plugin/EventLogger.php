<?php

declare(strict_types=1);

namespace AveProophPackage\Plugin;

use AveProophPackage\Logger\EventLogger as IEventLogger;
use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\Async\AsyncMessage;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;

/**
 * Class EventLogger
 *
 * @package AveProophPackage\Plugin
 * @author Averor <averor.dev@gmail.com>
 */
class EventLogger extends AbstractPlugin
{
    /** @var IEventLogger */
    protected $logger;

    /**
     * @param IEventLogger $logger
     */
    public function __construct(IEventLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param MessageBus $eventBus
     */
    public function attachToMessageBus(MessageBus $eventBus) : void
    {
        $this->listenerHandlers[] = $eventBus->attach(
            MessageBus::EVENT_DISPATCH,
            function (ActionEvent $actionEvent): void {
                $message = $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE);
                if (
                    $message instanceof AsyncMessage
                    && isset($message->metadata()['handled-async'])
                    && $message->metadata()['handled-async'] === true
                ) {
                    return;
                }
                $this->logger->logEvent($message);
            },
            MessageBus::PRIORITY_INITIALIZE
        );
    }
}
