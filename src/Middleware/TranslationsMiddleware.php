<?php

namespace App\Middleware;

use App\Util\VisitorInfoUtil;
use Symfony\Contracts\Translation\LocaleAwareInterface;

/*
    This middleware set translations
*/

class TranslationsMiddleware
{
    private LocaleAwareInterface $translator;
    private VisitorInfoUtil $visitorInfoUtil;
    
    public function __construct(
        LocaleAwareInterface $translator, 
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->translator = $translator;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    public function onKernelRequest(): void
    {
        // get visitor language
        $language = $this->visitorInfoUtil->getVisitorLanguage();

        if ($language == null or $language == 'host' or $language == 'unknown') {
            $this->translator->setLocale('en');
        } else {
            // set visitor locale
            $this->translator->setLocale($language);
        }
    }
}
