<?php

namespace App\Tests\Command;

use PHPUnit\Framework\TestCase;
use App\Manager\ProjectsManager;
use Symfony\Component\Console\Application;
use App\Command\UpdateProjectsListCommand;
use PHPUnit\Framework\MockObject\MockObject;
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
    private ProjectsManager|MockObject $projectsManagerMock;

    protected function setUp(): void
    {
        parent::setUp();

        // create a mock of ProjectsManager
        $this->projectsManagerMock = $this->createMock(ProjectsManager::class);
    }

    /**
     * Test the UpdateProjectsListCommand with success
     *
     * @return void
     */
    public function testProjectsListUpdateCommandSuccess(): void
    {
        // mock the updateProjectList method to simulate success
        $this->projectsManagerMock->expects($this->once())->method('updateProjectList');

        // create a symfony console
        $application = new Application();
        $application->add(new UpdateProjectsListCommand($this->projectsManagerMock));

        // create a CommandTester to execute the command
        $command = $application->find('projects:list:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        // get output
        $output = $commandTester->getDisplay();

        // assert the output
        $this->assertStringContainsString('Projects list updated!', $output);
    }

    /**
     * Test the UpdateProjectsListCommand with failure
     *
     * @return void
     */
    public function testProjectsListUpdateCommandFailure(): void
    {
        // mock the updateProjectList method to throw an exception
        $this->projectsManagerMock->expects($this->once())->method('updateProjectList')
            ->willThrowException(new \Exception('Something went wrong'));

        // create a symfony console
        $application = new Application();
        $application->add(new UpdateProjectsListCommand($this->projectsManagerMock));

        // create a CommandTester to execute the command
        $command = $application->find('projects:list:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        // get output
        $output = $commandTester->getDisplay();

        // assert the output
        $this->assertStringContainsString('Process error: Something went wrong', $output);
    }
}
