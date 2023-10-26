<?php

namespace App\Controller;

use App\Manager\ErrorManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Error controller is a handler for visitors redirect
    Main controller what shows error pages by error code
*/

class ErrorController extends AbstractController
{
    private ErrorManager $errorManager;

    public function __construct(ErrorManager $errorManager) 
    {
        $this->errorManager = $errorManager;
    }

    // handle unknow error if code not used
    #[Route('/error', name: 'error_unknown')]
    public function unknownError(): void
    {
        $this->errorManager->handleErrorView('unknown');
    }

    // handle error by code
    #[Route('/error/{code}', methods: ['GET'], name: 'error_by_code')]
    public function errorHandle(string $code): void
    {
        // block handeling (maintenance, banned use only from app logic)
        if ($code == 'maintenance' or $code == 'banned') {
            $this->errorManager->handleErrorView('unknown');
        } else {
            $this->errorManager->handleErrorView($code);
        }
    }
}
