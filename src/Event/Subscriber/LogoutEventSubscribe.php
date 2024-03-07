<?php

namespace App\Event\Subscriber;

use App\Event\LogoutEvent;
use App\Service\Manager\LogManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogoutEventSubscribe implements EventSubscriberInterface
{
    private LogManager $logManager;

    public function __construct(LogManager $logManager)
    {
        $this->logManager = $logManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::NAME => 'onLogoutEvent',
        ];
    }

    public function onLogoutEvent(LogoutEvent $event)
    {
        $username = $event->getUsername();

        // log logout event
        $this->logManager->log('authenticator', 'user: '.$username.' logout');
    }
}
