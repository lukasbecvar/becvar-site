<?php

namespace App\Manager;

use App\Util\JsonUtil;
use App\Entity\Project;
use App\Util\CacheUtil;
use App\Util\SecurityUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthManager
 *
 * Projects manager provides methods to get/update the projects list
 *
 * @package App\Manager
*/
class ProjectsManager
{
    private JsonUtil $jsonUtil;
    private CacheUtil $cacheUtil;
    private LogManager $logManager;
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private EntityManagerInterface $entityManager;

    public function __construct(
        JsonUtil $jsonUtil,
        CacheUtil $cacheUtil,
        LogManager $logManager,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->jsonUtil = $jsonUtil;
        $this->cacheUtil = $cacheUtil;
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
    }

    /**
     * Updates the project list from a GitHub user's repositories
     *
     * @throws \App\Exception\AppErrorException Error to update project list
     *
     * @return void
     */
    public function updateProjectList(): void
    {
        // get github link
        $githubLink = $_ENV['GITHUB_LINK'];

        // strip link
        $githubUser = str_replace('https://github.com/', '', $githubLink);
        $githubUser = str_replace('/', '', $githubUser);

        // get repos form github
        $repos = $this->jsonUtil->getJson('https://api.github.com/users/' . $githubUser . '/repos');

        // delete all projects from table
        $this->dropProjects();

        // reset projects row index
        $this->resetIndex();

        // update projects
        foreach ($repos as $repo) {
            // get & escape values
            $name = $repo['name'];
            $language = $repo['language'];
            $htmlUrl = $repo['html_url'];

            // check if description is null
            if ($repo['description'] == null) {
                $description = $repo['name'];
            } else {
                // get repository description (with escape)
                $description = $this->securityUtil->escapeString($repo['description']);
            }

            // check if repo is profile readme
            if ($name != $githubUser) {
                // check if repo is not fork
                if ($repo['fork'] != true) {
                    // check if repo archived
                    if ($repo['archived'] == true) {
                        $status = 'closed';
                    } else {
                        $status = 'open';
                    }

                    // init project entity
                    $project = new Project();

                    // set project value
                    $project->setName($name)
                        ->setDescription($description)
                        ->setTechnology($language)
                        ->setLink($htmlUrl)
                        ->setStatus($status);

                    // try to insert project
                    try {
                        $this->entityManager->persist($project);
                        $this->entityManager->flush();
                    } catch (\Exception $e) {
                        $this->logManager->log('project-update', 'error to update project list');
                        $this->errorManager->handleError(
                            'error to save project: ' . $e->getMessage(),
                            Response::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }
                }
            }
        }

        // delete projects list cache
        $this->cacheUtil->deleteValue('projects-list-closed');
        $this->cacheUtil->deleteValue('projects-list-open');
        $this->cacheUtil->deleteValue('projects-count');

        // log process success
        $this->logManager->log('project-update', 'project list updated!');
    }

    /**
     * Drops all projects from the database
     *
     * @throws \App\Exception\AppErrorException Error to drop projects
     *
     * @return void
     */
    public function dropProjects(): void
    {
        // get projects repository
        $repository = $this->entityManager->getRepository(Project::class);

        // get projects entitys
        $data = $repository->findAll();

        // delete all projects
        foreach ($data as $item) {
            $this->entityManager->remove($item);
        }

        // update table
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'error to delete projects list: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Resets the AUTO_INCREMENT value for the projects table
     *
     * @throws \App\Exception\AppErrorException Error to reset projects index
     *
     * @return void
     */
    public function resetIndex(): void
    {
        $tableName = $this->entityManager->getClassMetadata(Project::class)->getTableName();
        $sql = 'ALTER TABLE ' . $tableName . ' AUTO_INCREMENT = 0';
        try {
            $this->entityManager->getConnection()->executeQuery($sql);
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'error to reset projects index: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Gets the list of projects based on their status
     *
     * @param string $status The status of the projects to get
     *
     * @throws \App\Exception\AppErrorException Error to get projects list
     *
     * @return Project[]|null The list of projects
     */
    public function getProjectsList(string $status): ?array
    {
        try {
            // check if projects list is cached
            if ($this->cacheUtil->isCatched('projects-list-' . $status)) {
                // get projects list from cache
                $projectsList = $this->cacheUtil->getValue('projects-list-' . $status)->get();
            } else {
                // get projects list from database
                $projectsList = $this->entityManager->getRepository(Project::class)->findBy(['status' => $status]);

                // cache projects list
                $this->cacheUtil->setValue('projects-list-' . $status, $projectsList, 60 * 60 * 24 * 30);
            }

            // return projects list
            return $projectsList;
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'error to get projects list: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            return null;
        }
    }

    /**
     * Gets the total count of projects
     *
     * @throws \App\Exception\AppErrorException Error to get projects count
     *
     * @return int The total count of projects
     */
    public function getProjectsCount(): ?int
    {
        try {
            if ($this->cacheUtil->isCatched('projects-count')) {
                // get projects count from cache
                $projectsCount = $this->cacheUtil->getValue('projects-count')->get();
            } else {
                // get projects count from database
                $projectsCount = $this->entityManager->getRepository(Project::class)->count([]);

                // cache projects count
                $this->cacheUtil->setValue('projects-count', $projectsCount, 60 * 60 * 24 * 30);
            }

            return $projectsCount;
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'error to get projects list: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            return null;
        }
    }
}
