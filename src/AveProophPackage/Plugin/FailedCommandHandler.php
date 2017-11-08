<?php

declare(strict_types=1);

namespace AveProophPackage\Plugin;

use AveProophPackage\Logger\FailedCommandLogger;
use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;

/**
 * Class FailedCommandHandler
 *
 * @package AveProophPackage\Plugin
 * @author Averor <averor.dev@gmail.com>
 */
class FailedCommandHandler extends AbstractPlugin
{
    /** @var FailedCommandLogger */
    protected $logger;

    /**
     * @param FailedCommandLogger $logger
     */
    public function __construct(FailedCommandLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param MessageBus $commandBus
     */
    public function attachToMessageBus(MessageBus $commandBus) : void
    {
        $this->listenerHandlers[] = $commandBus->attach(
            MessageBus::EVENT_FINALIZE,
            function (ActionEvent $actionEvent): void {

                /** @var \Throwable $exception */
                if (!$exception = $actionEvent->getParam(MessageBus::EVENT_PARAM_EXCEPTION)) {
                    return;
                }

                $handler = $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLER);
                if (is_object($handler)) {
                    $handler = get_class($handler);
                }

                $this->logger->logFailedCommand(
                    $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE),
                    $handler,
                    $exception
                );
            },
            MessageBus::PRIORITY_INITIALIZE
        );
    }
}
