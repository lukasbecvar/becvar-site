<?php

namespace App\Manager;

use App\Util\JsonUtil;
use App\Entity\Project;
use App\Util\SecurityUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Projects manager provides get/update projects list
*/

class ProjectsManager
{
    private JsonUtil $jsonUtil;
    private LogManager $logManager;
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private EntityManagerInterface $entityManager;

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
            $description = $this->securityUtil->escapeString($repo['description']);
            
            // check if repo is profile readme
            if ($name != $github_user) {

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

        $this->logManager->log('project-update', 'project list updated!');
    }

    public function dropProjects(): void 
    {
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

    public function getProjectsList(string $status): ?array 
    {
        try {
            return $this->entityManager->getRepository(Project::class)->findBy(['status' => $status]);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get projects list: '.$e->getMessage(), 500);
            return [];
        }
    }

    public function getProjectsCount(): int
    {
        try {
            return count($this->entityManager->getRepository(Project::class)->findAll());
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get projects list: '.$e->getMessage(), 500);
            return 0;
        }   
    }

    public function resetIndex(): void 
    {
        // reset table AUTO_INCREMENT
        $tableName = $this->entityManager->getClassMetadata(Project::class)->getTableName(); 
        $sql = 'ALTER TABLE '.$tableName.' AUTO_INCREMENT = 0';
        try {
            $this->entityManager->getConnection()->executeQuery($sql);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to reset projects index: '.$e->getMessage(), 500);
        }
    }
}
