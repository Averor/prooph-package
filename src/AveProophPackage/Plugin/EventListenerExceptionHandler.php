<?php

declare(strict_types=1);

namespace AveProophPackage\Plugin;

use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;
use Psr\Log\LoggerInterface;

/**
 * Class EventListenerExceptionHandler
 *
 * @package AveProophPackage\Plugin
 * @author Averor <averor.dev@gmail.com>
 */
class EventListenerExceptionHandler extends AbstractPlugin
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param MessageBus $messageBus
     */
    public function attachToMessageBus(MessageBus $messageBus) : void
    {
        $this->listenerHandlers[] = $messageBus->attach(
            MessageBus::EVENT_FINALIZE,
            function (ActionEvent $actionEvent): void {

                /** @var \Throwable $exception */
                if (!$exception = $actionEvent->getParam(MessageBus::EVENT_PARAM_EXCEPTION)) {
                    return;
                }

                $this->logger->error(
                    'EventListener Exception [' . get_class($exception) . ']' . $exception->getMessage(),
                    ['exception' => $exception]
                );

                // Event listener cannot throw exception back to the app
                $actionEvent->setParam(MessageBus::EVENT_PARAM_EXCEPTION, null);
            },
            // \Prooph\ServiceBus\MessageBus::__construct defines listener attached with
            // default priority 1, throwing MessageDispatchException.
            2
        );
    }
}
