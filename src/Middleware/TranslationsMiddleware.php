<?php

namespace App\Middleware;

use App\Manager\VisitorManager;
use Symfony\Contracts\Translation\LocaleAwareInterface;

/*
    This middleware set translations
*/

class TranslationsMiddleware
{
    private LocaleAwareInterface $translator;
    private VisitorManager $visitorManager;
    
    public function __construct(
        LocaleAwareInterface $translator, 
        VisitorManager $visitorManager
    ) {
        $this->translator = $translator;
        $this->visitorManager = $visitorManager;
    }

    public function onKernelRequest(): void
    {
        // get visitor language
        $language = $this->visitorManager->getVisitorLanguage();

        if ($language == null or $language == 'host' or $language == 'unknown') {
            $this->translator->setLocale('en');
        } else {
            // set visitor locale
            $this->translator->setLocale($language);
        }
    }
}
