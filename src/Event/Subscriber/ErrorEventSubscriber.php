<?php

namespace App\Event\Subscriber;

use App\Event\ErrorEvent;
use App\Service\Manager\LogManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ErrorEventSubscriber
 *
 * Event subscriber for handling error events.
 * 
 * @package App\EventSubscriber
 */
class ErrorEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var LogManager The instance of LogManager.
     */
    private LogManager $logManager;

    /**
     * Constructor of the class.
     *
     * @param LogManager $logManager The instance of LogManager.
     */
    public function __construct(LogManager $logManager)
    {
        $this->logManager = $logManager;
    }

    /**
     * Returns an array of subscribed events that this object should listen to.
     *
     * @return array<string> An array containing event names and corresponding methods to be called when events are dispatched.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ErrorEvent::NAME => 'onErrorEvent',
        ];
    }

    /**
     * Method called when an error event is dispatched.
     *
     * @param ErrorEvent $event The object representing the error event.
     * @return void
     */
    public function onErrorEvent(ErrorEvent $event): void
    {
        // get error values
        $error_name = $event->getErrorName();
        $error_message = $event->getErrorMessage();

        // check if error is not log error
        if (
            !str_contains($error_message, 'log-error:') &&
            !str_contains($error_message, 'Unknown database')
        ) {
            $this->logManager->log($error_name, $error_message, true);
        }
    }
}
