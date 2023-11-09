<?php

namespace App\Middleware;

use App\Manager\VisitorManager;
use Symfony\Contracts\Translation\LocaleAwareInterface;

/*
    This middleware set translations
*/

class TranslationsMiddleware
{
    private VisitorManager $visitorManager;
    private LocaleAwareInterface $translator;
    
    public function __construct(
        VisitorManager $visitorManager,
        LocaleAwareInterface $translator 
    ) {
        $this->translator = $translator;
        $this->visitorManager = $visitorManager;
    }

    public function onKernelRequest(): void
    {
        // get visitor language
        $language = $this->visitorManager->getVisitorLanguage();

        // check unidentified languages
        if ($language == null or $language == 'host' or $language == 'unknown') {
            $this->translator->setLocale('en');
        } else {
            // set visitor locale
            $this->translator->setLocale($language);
        }
    }
}
