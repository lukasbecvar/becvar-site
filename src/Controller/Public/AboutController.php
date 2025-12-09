<?php

namespace App\Controller\Public;

use App\Util\AppUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AboutController
 *
 * About controller provides basic public about me page
 *
 * @package App\Controller\Public
*/
class AboutController extends AbstractController
{
    private AppUtil $appUtil;

    public function __construct(AppUtil $appUtil)
    {
        $this->appUtil = $appUtil;
    }

    /**
     * Handle about me page
     *
     * @return Response The about page view response
     */
    #[Route('/about', methods: ['GET'], name: 'public_about')]
    public function aboutPage(): Response
    {
        // calculate lukas becvar age xD
        $dateOfBirth = '1999-05-28';
        $age = date_diff(date_create($dateOfBirth), date_create('today'))->y;

        // render about page
        return $this->render('public/about.twig', [
            'age' => $age,
            'appUtil' => $this->appUtil,
            'githubLink' => $_ENV['GITHUB_LINK'],
            'twitterLink' => $_ENV['TWITTER_LINK'],
            'telegramLink' => $_ENV['TELEGRAM_LINK'],
            'contactEmail' => $_ENV['CONTACT_EMAIL'],
            "linkedInLink" => $_ENV['LINKEDIN_LINK'],
            'instagramLink' => $_ENV['INSTAGRAM_LINK']
        ]);
    }
}
