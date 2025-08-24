<?php

namespace App\Controller\Public;

use App\Util\AppUtil;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use App\Manager\ProjectsManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ProjectsController
 *
 * Projects controller provides list of public projects from the database
 * The project page displays projects from the database that are downloaded from the GitHub repositories using API
 *
 * @package App\Controller\Public
 */
class ProjectsController extends AbstractController
{
    private AppUtil $appUtil;
    private AuthManager $authManager;
    private ErrorManager $errorManager;
    private ProjectsManager $projectsManager;

    public function __construct(
        AppUtil $appUtil,
        AuthManager $authManager,
        ErrorManager $errorManager,
        ProjectsManager $projectsManager
    ) {
        $this->appUtil = $appUtil;
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
        $this->projectsManager = $projectsManager;
    }

    /**
     * Handle projects page
     *
     * @return Response The projects page view response
     */
    #[Route('/projects', methods: ['GET'], name: 'public_projects')]
    public function projectsList(): Response
    {
        // render projects page
        return $this->render('public/projects.twig', [
            // app util instance
            'appUtil' => $this->appUtil,

            // contact data
            'githubLink' => $_ENV['GITHUB_LINK'],
            'twitterLink' => $_ENV['TWITTER_LINK'],
            'contactEmail' => $_ENV['CONTACT_EMAIL'],
            'telegramLink' => $_ENV['TELEGRAM_LINK'],
            "linkedInLink" => $_ENV['LINKEDIN_LINK'],
            'instagramLink' => $_ENV['INSTAGRAM_LINK'],

            // projects data
            'projectsCount' => $this->projectsManager->getProjectsCount(),
            'openProjects' => $this->projectsManager->getProjectsList('open'),
            'closedProjects' => $this->projectsManager->getProjectsList('closed')
        ]);
    }

    /**
     * Handle update projects list with the latest data from GitHub API
     *
     * @return Response The response for updating projects, redirects to the admin database browser
     */
    #[Route('/projects/update', methods: ['GET'], name: 'public_projects_update')]
    public function projectsUpdate(): Response
    {
        // check if user is logged in
        if (!$this->authManager->isUserLogedin()) {
            $this->errorManager->handleError(
                msg: 'error to update project list: please login first',
                code: Response::HTTP_UNAUTHORIZED
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
