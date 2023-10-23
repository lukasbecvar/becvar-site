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

        if ($language == null) {
            $this->translator->setLocale('en');
        } else {
            // set visitor locale
            $this->translator->setLocale($language);
        }
    }
}
