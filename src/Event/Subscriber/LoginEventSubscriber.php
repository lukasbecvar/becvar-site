<?php

namespace App\Event\Subscriber;

use App\Event\LoginEvent;
use App\Service\Manager\LogManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LoginEventSubscriber
 * 
 * The LoginEventSubscriber class is responsible for subscribing to login events and performing actions accordingly.
 * 
 * @package App\Event\Subscriber
 */
class LoginEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var LogManager 
     * The LogManager instance for logging login events.
     */
    private LogManager $logManager;

    /**
     * Constructs a new LoginEventSubscriber instance.
     *
     * @param LogManager $logManager The LogManager instance for logging login events.
     */
    public function __construct(LogManager $logManager)
    {
        $this->logManager = $logManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array<mixed> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LoginEvent::NAME => 'onLoginEvent',
        ];
    }

    /**
     * Handles the login event.
     *
     * @param LoginEvent $event The login event object.
     */
    public function onLoginEvent(LoginEvent $event): void
    {
        $username = $event->getUsername();

        // log auth action
        $this->logManager->log('authenticator', 'user: '.$username.' logged in');
    }
}
