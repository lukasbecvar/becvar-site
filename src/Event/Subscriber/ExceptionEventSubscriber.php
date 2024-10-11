<?php

namespace App\Event\Subscriber;

use App\Manager\LogManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExceptionEventSubscriber
 *
 * Subscriber to handle error exceptions
 *
 * @package App\EventSubscriber
 */
class ExceptionEventSubscriber implements EventSubscriberInterface
{
    private LogManager $logManager;
    private LoggerInterface $logger;

    /**
     * List of error patterns that exclude from database log
     *
     * @var array<string>
     */
    private array $databaseLogBlockPattern = [
        'log-error:',
        'Unknown database',
        'Base table or view not found',
        'An exception occurred in the driver'
    ];

    /**
     * List of error patterns that exclude from exception log
     *
     * @var array<string>
     */
    private array $exceptionLogBlockPattern = [
        'No route found'
    ];

    public function __construct(LogManager $logManager, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logManager = $logManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to
     *
     * @return array<string> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    /**
     * Method called when the KernelEvents::EXCEPTION event is dispatched
     *
     * @param ExceptionEvent $event The event object
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        // get the exception
        $exception = $event->getThrowable();

        // get the error message
        $message = $exception->getMessage();

        // check if the event can be logged
        if ($this->canBeEventLogged($message, $this->databaseLogBlockPattern)) {
            // log the exception to database with the error code
            $this->logManager->log('exception', $message);
        }

        // check if the event can be logged
        if ($this->canBeEventLogged($message, $this->exceptionLogBlockPattern)) {
            // log the error message to exception log
            $this->logger->error($message);
        }
    }

    /**
     * Checks if an event can be logged based on the error message
     *
     * @param string $errorMessage The error message to be checked
     * @param array<string> $blockPatterns The list of patterns that can't be logged
     *
     * @return bool Returns true if the event can be dispatched, otherwise false
     */
    public function canBeEventLogged(string $errorMessage, array $blockPatterns = null): bool
    {
        $blockPatterns = $blockPatterns ?? $this->databaseLogBlockPattern;

        // loop through each blocked error pattern
        foreach ($blockPatterns as $pattern) {
            // check if the current pattern exists in the error message
            if (strpos($errorMessage, $pattern) !== false) {
                // if a blocked pattern is found, return false
                return false;
            }
        }

        // if no blocked patterns are found, return true
        return true;
    }
}
