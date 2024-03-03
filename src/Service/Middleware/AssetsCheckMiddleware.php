<?php

namespace App\Service\Middleware;

/**
 * Class AssetsCheckMiddleware
 *
 * This middleware checks if the required resources are installed.
 * 
 * @package App\Service\Middleware
 */
class AssetsCheckMiddleware
{
    /**
     * Check if assets are built.
     */
    public function onKernelRequest(): void
    {   
        if (!file_exists(__DIR__.'/../../../public/build/')) {
            die('Error: build resources not found, please contact service administrator & report this bug on email: '.$_ENV['CONTACT_EMAIL']);
        }
    }
}
