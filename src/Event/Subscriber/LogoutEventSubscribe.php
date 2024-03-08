<?php

namespace App\Event\Subscriber;

use App\Event\LogoutEvent;
use App\Service\Manager\LogManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogoutEventSubscribe
 * 
 * The LogoutEventSubscribe class is responsible for subscribing to logout events and performing actions accordingly.
 * 
 * @package App\Event\Subscriber
 */
class LogoutEventSubscribe implements EventSubscriberInterface
{
    /**
     * @var LogManager 
     * The LogManager instance for logging logout events.
     */
    private LogManager $logManager;

    /**
     * Constructs a new LogoutEventSubscribe instance.
     *
     * @param LogManager $logManager The LogManager instance for logging logout events.
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
            LogoutEvent::NAME => 'onLogoutEvent',
        ];
    }

    /**
     * Handles the logout event.
     *
     * @param LogoutEvent $event The logout event object.
     */
    public function onLogoutEvent(LogoutEvent $event): void
    {
        $username = $event->getUsername();

        // log logout event
        $this->logManager->log('authenticator', 'user: '.$username.' logout');
    }
}
