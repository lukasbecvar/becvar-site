<?php

namespace App\Controller;

use App\Helper\LogHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Home Controller is a main app controller for handle homepage
*/

class HomeController extends AbstractController
{

    private $logHelper;

    public function __construct(LogHelper $logHelper)
    {
        $this->logHelper = $logHelper;
    }

    #[Route(['/', '/home'], name: 'home')]
    public function index(): Response
    {

        // testing log
        $this->logHelper->log('testing log', 'testing values');

        return $this->render('home.html.twig');
    }
}
