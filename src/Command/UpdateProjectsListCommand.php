<?php

namespace App\Command;

use App\Manager\AuthManager;
use App\Manager\ProjectsManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateProjectsListCommand
 * 
 * Command for update projects list.
 * 
 * @package App\Command
 */
#[AsCommand(name: 'projects:list:update', description: 'Update projects list')]
class UpdateProjectsListCommand extends Command
{
    private ProjectsManager $projectsManager;

    public function __construct(ProjectsManager $projectsManager)
    {
        $this->projectsManager = $projectsManager;
        parent::__construct();
    }

    /**
     * Executes the command to update projects list.
     *
     * @param InputInterface  $input  The input interface.
     * @param OutputInterface $output The output interface.
     *
     * @return int The exit code of the command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        try {
            $this->projectsManager->updateProjectList();
            $io->success('Projects list updated!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error to update project list: '.$e->getMessage());
            return Command::FAILURE;
        }
    }
}
