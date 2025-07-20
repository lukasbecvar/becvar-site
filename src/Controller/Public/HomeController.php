<?php

namespace App\Controller\Public;

use App\Util\AppUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class HomeController
 *
 * Home controller provides main website homepage
 *
 * @package App\Controller\Public
*/
class HomeController extends AbstractController
{
    private AppUtil $appUtil;

    public function __construct(AppUtil $appUtil)
    {
        $this->appUtil = $appUtil;
    }

    /**
     * Handle home page
     *
     * @return Response The home page view response
     */
    #[Route(path: '/', methods: ['GET'], name: 'public_home')]
    public function homePage(): Response
    {
        return $this->render('public/home.twig', [
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
