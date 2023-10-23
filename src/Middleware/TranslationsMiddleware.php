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
        // get list of available translations
        $availableLocales = $this->translator->getFallbackLocales();
    
        // get visitor language
        $language = $this->visitorInfoUtil->getVisitorLanguage();
    
        // set language for visitors
        if (in_array($language, $availableLocales)) {
            $this->translator->setLocale($language);
        } else {
            $this->translator->setLocale('en');
        }
    }
}
