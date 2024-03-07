<?php

namespace App\Event\Subscriber;

use App\Event\LoginEvent;
use App\Service\Manager\LogManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginEventSubscriber implements EventSubscriberInterface
{
    private LogManager $logManager;

    public function __construct(LogManager $logManager)
    {
        $this->logManager = $logManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            LoginEvent::NAME => 'onLoginEvent',
        ];
    }

    public function onLoginEvent(LoginEvent $event)
    {
        $username = $event->getUsername();

        // log auth action
        $this->logManager->log('authenticator', 'user: '.$username.' logged in');
    }
}
