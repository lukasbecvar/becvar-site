<?php

namespace App\Controller;

use App\Manager\BanManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Home controller is a main app controller for homepage
*/

class HomeController extends AbstractController
{
    private $test;
    
    public function __construct(BanManager $test)
    {
        $this->test = $test;
    }

    #[Route(['/', '/home'], name: 'home')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }
}
