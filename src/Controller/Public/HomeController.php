<?php

namespace App\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class HomeController
 *
 * Home controller is a main app controller for homepage
 *
 * @package App\Controller\Public
*/
class HomeController extends AbstractController
{
    /**
     * Renders the public home page
     *
     * @return Response The response containing the rendered home page
     */
    #[Route(['/', '/home'], methods: ['GET'], name: 'public_home')]
    public function homePage(): Response
    {
        return $this->render('public/home.twig', [
            'githubLink' => $_ENV['GITHUB_LINK'],
            'twitterLink' => $_ENV['TWITTER_LINK'],
            'telegramLink' => $_ENV['TELEGRAM_LINK'],
            'contactEmail' => $_ENV['CONTACT_EMAIL'],
            'instagramLink' => $_ENV['INSTAGRAM_LINK']
        ]);
    }
}
