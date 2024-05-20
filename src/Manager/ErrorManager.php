<?php

namespace App\Manager;

use Twig\Environment;
use App\Util\SiteUtil;
use App\Event\ErrorEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthManager
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
     * This function returns void and kills the application process because it needs to be called outside of the main
     * Symfony process and from void functions, hence this inelegant solution is used.
     *
     * If the application is in dev mode, it returns a JSON response.
     *
     * @param string $msg The error message.
     * @param int $code The error code.
     *
     * @return Response.
     */
    public function handleError(string $msg, int $code): Response
    {
        // handle maintenance
        if ($msg == 'maintenance') {
            return die($this->handleErrorView('maintenance'));
        }

        // dispatch error event
        if ($this->canBeEventDispatched($msg)) {
            $this->eventDispatcher->dispatch(new ErrorEvent($code, 'internal-error', $msg), ErrorEvent::NAME);
        }

        // check if app is in dev mode
        if ($this->siteUtil->isDevMode()) {
            // build app error message
            $data = [
                'status' => 'error',
                'code' => $code,
                'message' => $msg
            ];

            // return JSON response
            return die(json_encode($data));
        } else {
            // return an error view response
            return die($this->handleErrorView(strval($code)));
        }
    }

    /**
     * Renders an error view based on the error code.
     *
     * @param string|int $code The error code.
     *
     * @return string The rendered error view.
     */
    public function handleErrorView(string|int $code)
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
