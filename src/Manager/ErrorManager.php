<?php

namespace App\Manager;

use Twig\Environment;
use App\Util\SiteUtil;
use Symfony\Component\HttpFoundation\Response;

/*
    Error manager provides error handle operations
*/

class ErrorManager
{
    private Environment $twig;
    private SiteUtil $siteUtil;

    public function __construct(Environment $twig, SiteUtil $siteUtil)
    {
        $this->twig = $twig;
        $this->siteUtil = $siteUtil;
    }

    public function handleError(string $msg, int $code): Response
    {
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

    public function handleErrorView(string $code)
    {
        // try to get view
        try {
            return $this->twig->render('errors/error-'.$code.'.html.twig');
        } catch (\Exception) {
            return $this->twig->render('errors/error-unknown.html.twig');
        }
    }
}
