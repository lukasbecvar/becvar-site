<?php

namespace App\Tests\Command;

use PHPUnit\Framework\TestCase;
use App\Manager\ProjectsManager;
use Symfony\Component\Console\Application;
use App\Command\UpdateProjectsListCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class UpdateProjectsListCommandTest
 *
 * Test the UpdateProjectsListCommand class
 *
 * @package App\Tests\Command
 */
class UpdateProjectsListCommandTest extends TestCase
{
    /**
     * Test the execute method
     *
     * @return void
     */
    public function testUpdateProjectsListCommand(): void
    {
        // mock ProjectsManager
        $projectsManager = $this->createMock(ProjectsManager::class);

        // set up the expected method calls and their return values
        $projectsManager->expects($this->once())->method('updateProjectList');

        // create the command
        $command = new UpdateProjectsListCommand($projectsManager);

        // create an application and add the command
        $application = new Application();
        $application->add($command);

        // create a command tester
        $commandTester = new CommandTester($command);

        // execute the command
        $commandTester->execute([]);

        // assert the output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Projects list updated!', $output);
    }
}
