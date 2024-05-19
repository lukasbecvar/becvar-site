<?php

namespace App\Event\Subscriber;

use App\Manager\LogManager;
use App\Manager\ErrorManager;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExceptionEventSubscriber
 *
 * Subscriber to handle internal (profiler) errors.
 *
 * @package App\EventSubscriber
 */
class ExceptionEventSubscriber implements EventSubscriberInterface
{
    private LogManager $logManager;
    private ErrorManager $errorManager;

    public function __construct(LogManager $logManager, ErrorManager $errorManager)
    {
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array<string> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * Method called when the KernelEvents::EXCEPTION event is dispatched.
     *
     * @param ExceptionEvent $event The event object
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        // get exception data
        $exception = $event->getThrowable();

        // get exception message
        $message = $exception->getMessage();

        // check if
        if ($this->errorManager->canBeEventDispatched($message)) {
            $this->logManager->log('exception', $message);
        }
    }
}
