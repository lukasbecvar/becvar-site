<?php

namespace App\Manager;

use Twig\Environment;
use App\Util\SiteUtil;

/*
    Error manager provides error handle operations
*/

class ErrorManager
{
    private $twig;
    private $siteUtil;

    public function __construct(Environment $twig, SiteUtil $siteUtil)
    {
        $this->twig = $twig;
        $this->siteUtil = $siteUtil;
    }

    public function handleError(string $msg, int $code): void
    {
        // check if app in devmode
        if ($this->siteUtil->isDevMode()) {

            // build app error msg
            $data = [
                'status' => 'error',
                'code' => $code,
                'message' => $msg
            ];

            // kill app & send error json
            die(json_encode($data));

        // error (for non devmode visitors)
        } else {
            die($this->handleErrorView($code));
        }
    }

    public function handleErrorView(string $code): void
    {
        // try to get view
        try {
            $view = $this->twig->render('errors/error-'.$code.'.html.twig');
        } catch (\Exception) {
            $view = $this->twig->render('errors/error-unknown.html.twig');
        }

        // die app & render error view
        die($view);
    }
}
