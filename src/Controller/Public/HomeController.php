<?php

namespace App\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Home controller is a main app controller for homepage
*/

class HomeController extends AbstractController
{
    #[Route(['/', '/home'], name: 'public_home')]
    public function homePage(): Response
    {
        return $this->render('public/home.html.twig', [
            'instagram_link' => $_ENV['INSTAGRAM_LINK'],
            'linkedin_link' => $_ENV['LINKEDIN_LINK'],
            'telegram_link' => $_ENV['TELEGRAM_LINK'],
            'contact_email' => $_ENV['CONTACT_EMAIL'],
            'twitter_link' => $_ENV['TWITTER_LINK'],
            'github_link' => $_ENV['GITHUB_LINK']
        ]);
    }
}
