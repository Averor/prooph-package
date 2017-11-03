<?php

declare(strict_types=1);

namespace AveProophPackage\Plugin;

use AveProophPackage\Logger\CommandLogger as ICommandLogger;
use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;

/**
 * Class CommandLogger
 *
 * @package AveProophPackage\Plugin
 * @author Averor <averor.dev@gmail.com>
 */
class CommandLogger extends AbstractPlugin
{
    /** @var ICommandLogger */
    protected $logger;

    /**
     * @param ICommandLogger $logger
     */
    public function __construct(ICommandLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param MessageBus $commandBus
     */
    public function attachToMessageBus(MessageBus $commandBus) : void
    {
        $this->listenerHandlers[] = $commandBus->attach(
            MessageBus::EVENT_DISPATCH,
            function (ActionEvent $actionEvent): void {
                $this->logger->logCommand(
                    $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE)
                );
            },
            MessageBus::PRIORITY_INITIALIZE
        );
    }
}
