<?php

namespace App\Manager;

use Twig\Environment;
use App\Util\SiteUtil;
use App\Event\ErrorEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ErrorManager
 *
 * ErrorManager provides error handling operations.
 *
 * @package App\Manager
 */
class ErrorManager
{
    private Environment $twig;
    private SiteUtil $siteUtil;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(Environment $twig, SiteUtil $siteUtil, EventDispatcherInterface $eventDispatcher)
    {
        $this->twig = $twig;
        $this->siteUtil = $siteUtil;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Handles errors based on the application's mode.
     *
     * @param string $msg The error message.
     * @param int $code The error code.
     *
     * @throws HttpException
     *
     * @return mixed
     */
    public function handleError(string $msg, int $code): mixed
    {
        // dispatch error event
        if ($this->canBeEventDispatched($msg)) {
            $this->eventDispatcher->dispatch(new ErrorEvent($code, 'internal-error', $msg), ErrorEvent::NAME);
        }

        // protect message on production env
        if (!$this->siteUtil->isDevMode()) {
            $msg = 'internal-error';
        }

        // build app error message
        $data = [
            'status' => 'error',
            'code' => $code,
            'message' => $msg
        ];

        // throw HttpException with JSON response
        throw new HttpException($code, json_encode($data), null, [], $code);
    }

    /**
     * Renders an error view based on the error code.
     *
     * @param string|int $code The error code.
     *
     * @return string The rendered error view.
     */
    public function handleErrorView(string|int $code): string
    {
        try {
            return $this->twig->render('errors/error-' . $code . '.html.twig');
        } catch (\Exception) {
            return $this->twig->render('errors/error-unknown.html.twig');
        }
    }

    /**
     * Checks if an event can be dispatched based on the error message.
     *
     * @param string $errorMessage The error message to be checked.
     * @return bool Returns true if the event can be dispatched, otherwise false.
     */
    public function canBeEventDispatched(string $errorMessage): bool
    {
        // list of error patterns that should block event dispatch
        $blockedErrorPatterns = [
            'log-error:',
            'Unknown database',
            'Base table or view not found',
            'An exception occurred in the driver'
        ];

        // loop through each blocked error pattern
        foreach ($blockedErrorPatterns as $pattern) {
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
