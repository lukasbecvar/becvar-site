<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Home controller is a main app controller for homepage
*/

class HomeController extends AbstractController
{
    #[Route(['/', '/home'], name: 'public_home')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }
}
