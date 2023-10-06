<?php

namespace App\Controller\Public;

use App\Manager\ProjectsManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Projects controller provides public projects list
*/

class ProjectsController extends AbstractController
{
    private $projectsManager;

    public function __construct(ProjectsManager $projectsManager)
    {
        $this->projectsManager = $projectsManager;    
    }

    #[Route('/projects', name: 'public_projects')]
    public function index(): Response
    {
        // render projects page
        return $this->render('public/projects.html.twig', [
            'instagram_link' => $_ENV['INSTAGRAM_LINK'],
            'linkedin_link' => $_ENV['LINKEDIN_LINK'],
            'telegram_link' => $_ENV['TELEGRAM_LINK'],
            'contact_email' => $_ENV['CONTACT_EMAIL'],
            'twitter_link' => $_ENV['TWITTER_LINK'],
            'github_link' => $_ENV['GITHUB_LINK'],
            'open_projects' => $this->projectsManager->getProjectsList('open'),
            'closed_projects' => $this->projectsManager->getProjectsList('closed'),
        ]);
    }
}
