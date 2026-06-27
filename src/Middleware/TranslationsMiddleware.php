<?php

namespace App\Middleware;

use App\Util\VisitorInfoUtil;
use App\Manager\VisitorManager;
use Symfony\Contracts\Translation\LocaleAwareInterface;

/**
 * Class TranslationsMiddleware
 *
 * Middleware for set locale by visitor location
 *
 * @package App\Middleware
 */
class TranslationsMiddleware
{
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private LocaleAwareInterface $translator;

    public function __construct(VisitorManager $visitorManager, VisitorInfoUtil $visitorInfoUtil, LocaleAwareInterface $translator)
    {
        $this->translator = $translator;
        $this->visitorInfoUtil = $visitorInfoUtil;
        $this->visitorManager = $visitorManager;
    }

    /**
     * Set translation based on visitor location
     *
     * @return void
     */
    public function onKernelRequest(): void
    {
        $language = $this->visitorManager->getVisitorLanguage();
        if ($language == null) {
            $ipAddress = $this->visitorInfoUtil->getIP();
            $language = $this->visitorInfoUtil->getLocation($ipAddress)['country'];
        }

        // check unidentified languages
        if ($language == 'host' or $language == 'unknown') {
            $this->translator->setLocale('en');
        } else {
            // set visitor locale
            $this->translator->setLocale($language);
        }
    }
}
