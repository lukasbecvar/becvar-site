<?php

namespace App\Middleware;

/*
    This middleware check if requird resources is installed
*/

class AssetsCheckMiddleware
{
    public function onKernelRequest(): void
    {
        // check if assets is builded
        if (!file_exists(__DIR__.'/../../public/build/')) {
            die('Error: build resources not found, please contact service administrator & report this bug on email: '.$_ENV['CONTACT_EMAIL']);
        }
    }
}
