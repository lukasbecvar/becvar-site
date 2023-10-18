<?php

namespace App\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    About controller provides basic public about page
    Page with information about me
*/

class AboutController extends AbstractController
{
    #[Route('/about', name: 'public_about')]
    public function aboutPage(): Response
    {
        // calculate lukas becvar age xD
        $birth_date = explode('/', '05/28/1999');
        $age = (date('md', date('U', mktime(0, 0, 0, $birth_date[0], $birth_date[1], $birth_date[2]))) > date('md') ? ((date('Y') - $birth_date[2]) - 1) : (date('Y') - $birth_date[2]));

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
