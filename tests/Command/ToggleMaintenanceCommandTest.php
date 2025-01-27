<?php

namespace App\Tests\Command;

use Exception;
use App\Util\AppUtil;
use PHPUnit\Framework\TestCase;
use App\Command\ToggleMaintenanceCommand;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ToggleMaintenanceCommandTest
 *
 * Test cases for toggle maintenance command
 *
 * @package App\Tests\Command
 */
class ToggleMaintenanceCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private ToggleMaintenanceCommand $command;
    private AppUtil & MockObject $appUtilMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->appUtilMock = $this->createMock(AppUtil::class);

        // create command instance
        $this->command = new ToggleMaintenanceCommand($this->appUtilMock);
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * Test execute command when maintenance mode is enabled
     *
     * @return void
     */
    public function testExecuteCommandWhenMaintenanceModeIsEnabled(): void
    {
        // simulate maintenance mode is enabled
        $this->appUtilMock->method('getEnvValue')->willReturn('true');

        // expect call updateEnvValue with 'false' value
        $this->appUtilMock->expects($this->once())->method('updateEnvValue')
            ->with('MAINTENANCE_MODE', 'false');

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // assert result
        $this->assertStringContainsString('MAINTENANCE_MODE in .env has been set to true', $this->commandTester->getDisplay());
        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    /**
     * Test execute command when maintenance mode is disabled
     *
     * @return void
     */
    public function testExecuteCommandWhenMaintenanceModeIsDisabled(): void
    {
        // simulate maintenance mode is disabled
        $this->appUtilMock->method('getEnvValue')->willReturn('false');

        // expect call updateEnvValue with 'true' value
        $this->appUtilMock->expects($this->once())->method('updateEnvValue')
            ->with('MAINTENANCE_MODE', 'true');

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // assert result
        $this->assertStringContainsString('MAINTENANCE_MODE in .env has been set to true', $this->commandTester->getDisplay());
        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    /**
     * Test execute command when exception is thrown
     *
     * @return void
     */
    public function testExecuteCommandWhenExceptionIsThrown(): void
    {
        // mock exception thrown
        $this->appUtilMock->method('getEnvValue')
            ->willThrowException(new Exception('Failed to get environment value'));

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // assert result
        $this->assertStringContainsString('Process error: Failed to get environment value', $this->commandTester->getDisplay());
        $this->assertSame(Command::FAILURE, $exitCode);
    }
}
