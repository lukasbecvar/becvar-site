<?php

namespace App\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** 
 * Class AboutController
 * 
 * About controller provides basic public about page
 * Page with information about me
 * 
 * @package App\Controller\Public
*/
class AboutController extends AbstractController
{
    /**
     * Renders the public about page.
     *
     * @return Response The response containing the rendered about page.
     */
    #[Route('/about', methods: ['GET'], name: 'public_about')]
    public function aboutPage(): Response
    {
        // calculate lukas becvar age xD
        $date_of_birth = '1999-05-28';
        $age = date_diff(date_create($date_of_birth), date_create('today'))->y;

        return $this->render('public/about.html.twig', [
            'instagram_link' => $_ENV['INSTAGRAM_LINK'],
            'telegram_link' => $_ENV['TELEGRAM_LINK'],
            'contact_email' => $_ENV['CONTACT_EMAIL'],
            'twitter_link' => $_ENV['TWITTER_LINK'],
            'github_link' => $_ENV['GITHUB_LINK'],
            'age' => $age
        ]);
    }
}
