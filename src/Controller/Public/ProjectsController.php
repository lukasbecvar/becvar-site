<?php

namespace App\Controller\Public;

use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use App\Manager\ProjectsManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ProjectsController
 *
 * Projects controller provides a public list of projects
 * The project page displays projects from the database that are downloaded from the GitHub API
 *
 * @package App\Controller\Public
 */
class ProjectsController extends AbstractController
{
    private AuthManager $authManager;
    private ErrorManager $errorManager;
    private ProjectsManager $projectsManager;

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
     * Displays the public projects page
     *
     * @return Response The response containing the rendered projects page
     */
    #[Route('/projects', methods: ['GET'], name: 'public_projects')]
    public function projectsList(): Response
    {
        // render projects page
        return $this->render('public/projects.twig', [
            'githubLink' => $_ENV['GITHUB_LINK'],
            'twitterLink' => $_ENV['TWITTER_LINK'],
            'contactEmail' => $_ENV['CONTACT_EMAIL'],
            'telegramLink' => $_ENV['TELEGRAM_LINK'],
            'instagramLink' => $_ENV['INSTAGRAM_LINK'],
            'projectsCount' => $this->projectsManager->getProjectsCount(),
            'openProjects' => $this->projectsManager->getProjectsList('open'),
            'closedProjects' => $this->projectsManager->getProjectsList('closed'),
        ]);
    }

    /**
     * Updates the projects list
     * 
     * @throws \App\Exception\AppErrorException Error the user is not logged in
     *
     * @return Response The response for updating projects, redirects to the admin database browser
     */
    #[Route('/projects/update', methods: ['GET'], name: 'public_projects_update')]
    public function projectsUpdate(): Response
    {
        // check if user authorized
        if (!$this->authManager->isUserLogedin()) {
            return $this->errorManager->handleError(
                'error to update project list: please login first', 
                Response::HTTP_UNAUTHORIZED
            );
        }

        // update projects list
        $this->projectsManager->updateProjectList();

        // redirect to the admin database browser
        return $this->redirectToRoute('admin_database_browser', [
            'page' => 1,
            'table' => 'projects'
        ]);
    }
}
