<?php

namespace App\Middleware;

use Symfony\Contracts\Translation\TranslatorInterface;

/*
    This middleware set translation
*/

class TranslationsMiddleware
{
    private $translator;
    
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function onKernelRequest()
    {

        $this->translator->setLocale('cz');
    }
}
