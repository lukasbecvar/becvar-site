<?php

namespace App\Middleware;

use App\Util\VisitorInfoUtil;
use Symfony\Contracts\Translation\TranslatorInterface;

/*
    This middleware set translation
*/

class TranslationsMiddleware
{
    private $translator;
    private $visitorInfoUtil;
    
    public function __construct(
        TranslatorInterface $translator, 
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->translator = $translator;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    public function onKernelRequest()
    {
        // get visitor language
        $language = $this->visitorInfoUtil->getVisitorLanguage();

        // set language for czech visitors
        if ($language == 'cz') {
            $this->translator->setLocale('cz');
        } else {
            $this->translator->setLocale('en');
        }
    }
}
