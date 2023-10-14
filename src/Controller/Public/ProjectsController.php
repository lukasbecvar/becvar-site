<?php

namespace App\Controller\Public;

use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use App\Manager\ProjectsManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Projects controller provides public projects list
*/

class ProjectsController extends AbstractController
{
    private $authManager;
    private $errorManager;
    private $projectsManager;

    public function __construct(AuthManager $authManager, ErrorManager $errorManager, ProjectsManager $projectsManager)
    {
        $this->authManager = $authManager;    
        $this->errorManager = $errorManager;    
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

    #[Route('/projects/update', name: 'public_projects_update')]
    public function update(): Response
    {
        // check if user logged
        if ($this->authManager->isUserLogedin()) {
            $this->projectsManager->updateProjectList();
            return $this->redirectToRoute('admin_database_browser', [
                'table' => 'projects',
                'page' => 1
            ]);
        } else {
            $this->errorManager->handleError('error to update project list: please login first', 401);
        }
    }
}
