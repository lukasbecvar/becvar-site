<?php

namespace App\Manager;

use App\Util\JsonUtil;
use App\Entity\Project;
use App\Util\SecurityUtil;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AuthManager
 * 
 * Projects manager provides methods to get/update the projects list
 * 
 * @package App\Manager
*/
class ProjectsManager
{
    /**
     * @var JsonUtil
     * Instance of the JsonUtil for handling JSON-related utilities.
     */
    private JsonUtil $jsonUtil;

    /**
     * @var LogManager
     * Instance of the LogManager for handling log-related functionality.
     */
    private LogManager $logManager;

    /**
     * @var ErrorManager
     * Instance of the ErrorManager for handling error-related functionality.
     */
    private ErrorManager $errorManager;

    /**
     * @var SecurityUtil
     * Instance of the SecurityUtil for handling security-related utilities.
     */
    private SecurityUtil $securityUtil;

    /**
     * @var EntityManagerInterface
     * Instance of the EntityManagerInterface for interacting with the database.
     */
    private EntityManagerInterface $entityManager;

    /**
     * ProjectsManager constructor.
     *
     * @param JsonUtil               $jsonUtil
     * @param LogManager             $logManager
     * @param ErrorManager           $errorManager
     * @param SecurityUtil           $securityUtil
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        JsonUtil $jsonUtil, 
        LogManager $logManager, 
        ErrorManager $errorManager,
        SecurityUtil $securityUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->jsonUtil = $jsonUtil;
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
    }

    /**
     * Updates the project list from a GitHub user's repositories.
     *
     * @return void
     */
    public function updateProjectList(): void 
    {
        // get github link
        $github_link = $_ENV['GITHUB_LINK'];

        // strip link
        $github_user = str_replace('https://github.com/', '', $github_link);
        $github_user = str_replace('/', '', $github_user);

        // get repos form github
        $repos = $this->jsonUtil->getJson('https://api.github.com/users/'.$github_user.'/repos');

        // delete all projects from table
        $this->dropProjects();

        // reset projects row index
        $this->resetIndex();

        // update projects
        foreach($repos as $repo) {

            // get & escape values
            $name = $repo['name'];
            $language = $repo['language'];
            $html_url = $repo['html_url'];

            // check if description is null
            if ($repo['description'] == null) {
                $description = $repo['name'];
            } else {

                // get repository description (with escape)
                $description = $this->securityUtil->escapeString($repo['description']);
            }
            
            // check if repo is profile readme
            if ($name != $github_user) {

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
                    $project->setName($name);
                    $project->setDescription($description);
                    $project->setTechnology($language);
                    $project->setLink($html_url);
                    $project->setStatus($status);

                    // try to insert project
                    try {
                        $this->entityManager->persist($project);
                        $this->entityManager->flush();
                    } catch (\Exception $e) {
                        $this->logManager->log('project-update', 'error to update project list');
                        $this->errorManager->handleError('error to save project: '.$e->getMessage(), 500);
                    }
                }
            }
        }

        $this->logManager->log('project-update', 'project list updated!');
    }

    /**
     * Drops all projects from the database.
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
            $this->errorManager->handleError('error to delete projects list: '.$e->getMessage(), 500);
        }
    }

    /**
     * Resets the AUTO_INCREMENT value for the projects table.
     *
     * @return void
     */
    public function resetIndex(): void 
    {
        $tableName = $this->entityManager->getClassMetadata(Project::class)->getTableName(); 
        $sql = 'ALTER TABLE '.$tableName.' AUTO_INCREMENT = 0';
        try {
            $this->entityManager->getConnection()->executeQuery($sql);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to reset projects index: '.$e->getMessage(), 500);
        }
    }

    /**
     * Gets the list of projects based on their status.
     *
     * @param string $status
     *
     * @return Project[]|null
     */
    public function getProjectsList(string $status): ?array 
    {
        try {
            return $this->entityManager->getRepository(Project::class)->findBy(['status' => $status]);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get projects list: '.$e->getMessage(), 500);
            return [];
        }
    }

    /**
     * Gets the total count of projects.
     *
     * @return int
     */
    public function getProjectsCount(): int
    {
        try {
            return count($this->entityManager->getRepository(Project::class)->findAll());
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get projects list: '.$e->getMessage(), 500);
            return 0;
        }   
    }
}
