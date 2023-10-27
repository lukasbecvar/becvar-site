<?php

namespace App\Controller;

use App\Util\SiteUtil;
use App\Manager\ErrorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Error controller is a handler for visitors redirect
    Main controller what shows error pages by error code
*/

class ErrorController extends AbstractController
{
    private SiteUtil $siteUtil;
    private ErrorManager $errorManager;

    public function __construct(SiteUtil $siteUtil, ErrorManager $errorManager) 
    {
        $this->siteUtil = $siteUtil;
        $this->errorManager = $errorManager;
    }

    // handle error
    #[Route('/error', methods: ['GET'], name: 'error_by_code')]
    public function errorHandle(Request $request): void
    {
        // get error code
        $code = $this->siteUtil->getQueryString('code', $request);

        // block handeling (maintenance, banned use only from app logic)
        if ($code == 'maintenance' or $code == 'banned' or $code == null) {
            $this->errorManager->handleErrorView('unknown');
        } else {
            $this->errorManager->handleErrorView($code);
        }
    }
}
