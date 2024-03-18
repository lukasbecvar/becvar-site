<?php

namespace App\Service\Middleware;

use App\Service\Manager\VisitorManager;
use Symfony\Contracts\Translation\LocaleAwareInterface;

/**
 * Class TranslationsMiddleware
 *
 * This middleware sets translations based on the visitor's language.
 * 
 * @package App\Service\Middleware
 */
class TranslationsMiddleware
{
    /**
     * @var VisitorManager
     * Instance of the VisitorManager for handling visitor-related functionality.
     */
    private VisitorManager $visitorManager;

    /**
     * @var LocaleAwareInterface
     * Instance of the LocaleAwareInterface for handling locale-aware translation.
     */
    private LocaleAwareInterface $translator;
    
    /**
     * TranslationsMiddleware constructor.
     *
     * @param VisitorManager       $visitorManager
     * @param LocaleAwareInterface $translator
     */
    public function __construct(
        VisitorManager $visitorManager,
        LocaleAwareInterface $translator 
    ) {
        $this->translator = $translator;
        $this->visitorManager = $visitorManager;
    }

    /**
     * Set translations based on the visitor's language.
     */
    public function onKernelRequest(): void
    {
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
