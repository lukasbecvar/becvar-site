<?php

namespace App\Manager;

use Twig\Environment;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ErrorManager
 *
 * ErrorManager provides error handling operations
 *
 * @package App\Manager
 */
class ErrorManager
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Handles errors based on the application's mode
     *
     * @param string $msg The error message
     * @param int $code The error code
     *
     * @throws HttpException
     *
     * @return mixed
     */
    public function handleError(string $msg, int $code): mixed
    {
        // throw HttpException with JSON response
        throw new HttpException($code, $msg, null, [], $code);
    }

    /**
     * Renders an error view based on the error code
     *
     * @param string|int $code The error code
     *
     * @return string The rendered error view
     */
    public function getErrorView(string|int $code): string
    {
        try {
            return $this->twig->render('errors/error-' . $code . '.twig');
        } catch (\Exception) {
            return $this->twig->render('errors/error-unknown.twig');
        }
    }
}
