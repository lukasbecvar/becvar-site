<?php

namespace App\Controller\Public;

use App\Util\AppUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
            'githubLink' => $this->appUtil->getEnvValue('GITHUB_LINK'),
            'twitterLink' => $this->appUtil->getEnvValue('TWITTER_LINK'),
            'contactEmail' => $this->appUtil->getEnvValue('CONTACT_EMAIL'),
            'telegramLink' => $this->appUtil->getEnvValue('TELEGRAM_LINK'),
            "linkedInLink" => $this->appUtil->getEnvValue('LINKEDIN_LINK'),
            'instagramLink' => $this->appUtil->getEnvValue('INSTAGRAM_LINK')
        ]);
    }
}
