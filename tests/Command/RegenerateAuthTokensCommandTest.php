<?php

namespace App\Tests\Command;

use App\Manager\AuthManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use App\Command\RegenerateAuthTokensCommand;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class RegenerateAuthTokensCommandTest
 *
 * Test the RegenerateAuthTokensCommand class
 *
 * @package App\Tests\Command
 */
class RegenerateAuthTokensCommandTest extends TestCase
{
    private AuthManager|MockObject $authManagerMock;

    protected function setUp(): void
    {
        parent::setUp();

        // create a mock of AuthManager
        $this->authManagerMock = $this->createMock(AuthManager::class);
    }

    /**
     * Test the RegenerateAuthTokensCommand with success
     *
     * @return void
     */
    public function testRegenerateAuthTokensCommandSuccess(): void
    {
        // mock the return value of regenerateUsersTokens to simulate success
        $this->authManagerMock
            ->expects($this->once())
            ->method('regenerateUsersTokens')
            ->willReturn(['status' => true]);

        // create a Symfony Console Application
        $application = new Application();
        $application->add(new RegenerateAuthTokensCommand($this->authManagerMock));

        // create a CommandTester to execute the command
        $command = $application->find('auth:tokens:regenerate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        // get output
        $output = $commandTester->getDisplay();

        // assert output
        $this->assertStringContainsString('All tokens is regenerated!', $output);
    }

    /**
     * Test the RegenerateAuthTokensCommand with failure
     *
     * @return void
     */
    public function testRegenerateAuthTokensCommandFailure(): void
    {
        // mock the return value of regenerateUsersTokens to simulate failure
        $this->authManagerMock
            ->expects($this->once())
            ->method('regenerateUsersTokens')
            ->willReturn(['status' => false, 'message' => 'Error message']);

        // create a Symfony Console Application
        $application = new Application();
        $application->add(new RegenerateAuthTokensCommand($this->authManagerMock));

        // create a CommandTester to execute the command
        $command = $application->find('auth:tokens:regenerate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        // get output
        $output = $commandTester->getDisplay();

        // assert the output
        $this->assertStringContainsString('Process error: Error message', $output);
    }
}
