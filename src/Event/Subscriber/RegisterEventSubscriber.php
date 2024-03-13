<?php

namespace App\Event\Subscriber;

use App\Event\RegisterEvent;
use App\Service\Manager\LogManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class RegisterEventSubscriber
 * 
 * The RegisterEventSubscriber class is responsible for subscribing to register events and performing actions accordingly.
 * 
 * @package App\Event\Subscriber
 */
class RegisterEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var LogManager 
     * The LogManager instance for logging register events.
     */
    private LogManager $logManager;

    /**
     * Constructs a new RegisterEventSubscriber instance.
     *
     * @param LogManager $logManager The LogManager instance for logging register events.
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
            RegisterEvent::NAME => 'onRegisterEvent',
        ];
    }

    /**
     * Handles the register event.
     *
     * @param RegisterEvent $event The register event object.
     */
    public function onRegisterEvent(RegisterEvent $event): void
    {
        $username = $event->getUsername();

        // log registration event
        $this->logManager->log('authenticator', 'registration new user: '.$username.' registred');
    }
}
