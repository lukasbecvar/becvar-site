<?php

namespace App\Manager;

use Twig\Environment;
use App\Util\SiteUtil;
use Symfony\Component\HttpFoundation\Response;

/**
 * ErrorManager provides error handling operations.
 */
class ErrorManager
{
    /** * @var Environment */
    private Environment $twig;
    
    /** * @var SiteUtil */
    private SiteUtil $siteUtil;

    /** * @var LogManager */
    private LogManager $logManager;

    /**
     * ErrorManager constructor.
     *
     * @param Environment $twig
     * @param SiteUtil    $siteUtil
     * @param LogManager  $logManager
     */
    public function __construct(Environment $twig, SiteUtil $siteUtil, LogManager $logManager)
    {
        $this->twig = $twig;
        $this->siteUtil = $siteUtil;
        $this->logManager = $logManager;
    }

    /**
     * Handles errors based on the application's mode.
     *
     * @param string $msg The error message.
     * @param int $code The error code.
     *
     * @return Response A Response object representing the error.
     */
    public function handleError(string $msg, int $code): Response
    {
        // log error action
        $this->logManager->log('internal-error', $msg.', code: '.$code);

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
     * @param string $code The error code.
     *
     * @return string The rendered error view.
     */
    public function handleErrorView(string $code)
    {
        try {
            return $this->twig->render('errors/error-'.$code.'.html.twig');
        } catch (\Exception) {
            return $this->twig->render('errors/error-unknown.html.twig');
        }
    }
}
