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

    public function __construct(AuthManager $authManager, ErrorManager $errorManager, ProjectsManager $projectsManager)
    {
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
        $this->projectsManager = $projectsManager;
    }

    /**
     * Render projects page
     *
     * @return Response The projects page view response
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
            "linkedInLink" => $_ENV['LINKEDIN_LINK'],
            'instagramLink' => $_ENV['INSTAGRAM_LINK'],

            // projects data
            'projectsCount' => $this->projectsManager->getProjectsCount(),
            'openProjects' => $this->projectsManager->getProjectsList('open'),
            'closedProjects' => $this->projectsManager->getProjectsList('closed'),
        ]);
    }

    /**
     * Update projects list with the latest data from the GitHub API
     *
     * @throws \Exception Error the user is not logged in
     *
     * @return Response The response for updating projects, redirects to the admin database browser
     */
    #[Route('/projects/update', methods: ['GET'], name: 'public_projects_update')]
    public function projectsUpdate(): Response
    {
        // check if user is logged in
        if (!$this->authManager->isUserLogedin()) {
            $this->errorManager->handleError(
                'error to update project list: please login first',
                Response::HTTP_UNAUTHORIZED
            );
        }

        // update projects list
        $this->projectsManager->updateProjectList();

        // redirect to the database browser component
        return $this->redirectToRoute('admin_database_browser', [
            'table' => 'projects',
            'page' => 1
        ]);
    }
}
