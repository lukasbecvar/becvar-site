<?php

namespace App\Command;

use Exception;
use App\Manager\ProjectsManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateProjectsListCommand
 *
 * Command for update projects repositories list in database
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
     * Execute command to update projects list
     *
     * @param InputInterface $input The input interface
     * @param OutputInterface $output The output interface
     *
     * @return int The command exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // set user ip address for cli console
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        try {
            // update projects list
            $this->projectsManager->updateProjectList();

            // success message
            $io->success('Projects list updated');
            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Process error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
