<?php

namespace App\Helper;

use Twig\Environment;

/*
    Error helper provides error handle operations
*/

class ErrorHelper
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function handleError(string $msg, int $code): void
    {
        // check if app in devmode
        if ($_ENV['APP_ENV'] == 'dev') {

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
