<?php

namespace App\Controller\Public;

use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use App\Manager\ProjectsManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Projects controller provides a public list of projects.
 * The project page displays projects from the database that are downloaded from the GitHub API.
 */
class ProjectsController extends AbstractController
{
    /** * @var AuthManager */
    private AuthManager $authManager;

    /** * @var ErrorManager */
    private ErrorManager $errorManager;

    /** * @var ProjectsManager */
    private ProjectsManager $projectsManager;

    /**
     * ProjectsController constructor.
     *
     * @param AuthManager     $authManager
     * @param ErrorManager    $errorManager
     * @param ProjectsManager $projectsManager
     */
    public function __construct(
        AuthManager $authManager, 
        ErrorManager $errorManager, 
        ProjectsManager $projectsManager
    ) {
        $this->authManager = $authManager;    
        $this->errorManager = $errorManager;    
        $this->projectsManager = $projectsManager;    
    }

    /**
     * Displays the public projects page.
     *
     * @return Response The response containing the rendered projects page.
     */
    #[Route('/projects', name: 'public_projects')]
    public function index(): Response
    {
        // render projects page
        return $this->render('public/projects.html.twig', [
            'instagram_link' => $_ENV['INSTAGRAM_LINK'],
            'telegram_link' => $_ENV['TELEGRAM_LINK'],
            'contact_email' => $_ENV['CONTACT_EMAIL'],
            'twitter_link' => $_ENV['TWITTER_LINK'],
            'github_link' => $_ENV['GITHUB_LINK'],
            'projects_count' => $this->projectsManager->getProjectsCount(),
            'open_projects' => $this->projectsManager->getProjectsList('open'),
            'closed_projects' => $this->projectsManager->getProjectsList('closed'),
        ]);
    }

    /**
     * Updates the projects list.
     *
     * @return Response The response for updating projects, redirects to the admin database browser.
     */
    #[Route('/projects/update', name: 'public_projects_update')]
    public function projectsUpdate(): Response
    {
        // check if user logged
        if ($this->authManager->isUserLogedin()) {
            
            // update projects list
            $this->projectsManager->updateProjectList();
            
            return $this->redirectToRoute('admin_database_browser', [
                'table' => 'projects',
                'page' => 1
            ]);
        } else {
            return $this->errorManager->handleError('error to update project list: please login first', 401);
        }
    }
}
