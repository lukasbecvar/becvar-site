<?php

namespace App\Controller;

use App\Helper\ErrorHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Error controller is a handler for visitors redirect
*/

class ErrorController extends AbstractController
{

    private $errorHelper;

    public function __construct(ErrorHelper $errorHelper) 
    {
        $this->errorHelper = $errorHelper;
    }

    // handle unknow error if code not used
    #[Route('/error', name: 'error_unknown')]
    public function unknownError(): void
    {
        $this->errorHelper->handleErrorView('unknown');
    }

    // handle error by code
    #[Route('/error/{code}', methods: ['GET'], name: 'error_by_code')]
    public function errorHandle(string $code): void
    {
        // block maintenance handeling (maintenance, banned use only from app logic)
        if ($code == 'maintenance' or $code == 'banned') {
            $this->errorHelper->handleErrorView('unknown');
        } else {
            $this->errorHelper->handleErrorView($code);
        }
    }
}
