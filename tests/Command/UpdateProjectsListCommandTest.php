<?php

namespace App\Tests\Command;

use Exception;
use PHPUnit\Framework\TestCase;
use App\Manager\ProjectsManager;
use App\Command\UpdateProjectsListCommand;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class UpdateProjectsListCommandTest
 *
 * Test cases for update projects list command
 *
 * @package App\Tests\Command
 */
class UpdateProjectsListCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private UpdateProjectsListCommand $command;
    private ProjectsManager & MockObject $projectsManagerMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->projectsManagerMock = $this->createMock(ProjectsManager::class);

        // instantiate command instance
        $this->command = new UpdateProjectsListCommand($this->projectsManagerMock);
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * Test execute update projects list command with success response
     *
     * @return void
     */
    public function testExecuteUpdateProjectsListCommandWithSuccess(): void
    {
        // expect projects update call
        $this->projectsManagerMock->expects($this->once())->method('updateProjectList');

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // get command output
        $output = $this->commandTester->getDisplay();

        // assert command output
        $this->assertStringContainsString('Projects list updated!', $output);
        $this->assertEquals(Command::SUCCESS, $exitCode);
    }

    /**
     * Test execute update projects list command with failure response
     *
     * @return void
     */
    public function testExecuteUpdateProjectsListCommandWithFailure(): void
    {
        // expect projects update call and sumulate failure response
        $this->projectsManagerMock->expects($this->once())->method('updateProjectList')
            ->willThrowException(new Exception('Something went wrong'));

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // get command output
        $output = $this->commandTester->getDisplay();

        // assert command output
        $this->assertStringContainsString('Process error: Something went wrong', $output);
        $this->assertEquals(Command::FAILURE, $exitCode);
    }
}
