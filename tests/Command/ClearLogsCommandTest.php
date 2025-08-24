<?php

namespace App\Tests\Command;

use Exception;
use App\Entity\Log;
use App\Util\AppUtil;
use PHPUnit\Framework\TestCase;
use App\Manager\DatabaseManager;
use App\Command\ClearLogsCommand;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ClearLogsCommandTest
 *
 * Test cases for clear logs command
 *
 * @package App\Tests\Command
 */
class ClearLogsCommandTest extends TestCase
{
    private ClearLogsCommand $command;
    private CommandTester $commandTester;
    private AppUtil & MockObject $appUtil;
    private DatabaseManager & MockObject $databaseManager;

    protected function setUp(): void
    {
        // mock dependencies
        $this->appUtil = $this->createMock(AppUtil::class);
        $this->databaseManager = $this->createMock(DatabaseManager::class);

        // create command instance
        $this->command = new ClearLogsCommand($this->appUtil, $this->databaseManager);
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * Test execute command when confirmation is declined
     *
     * @return void
     */
    public function testExecuteCommandWhenConfirmationIsDeclined(): void
    {
        // mock command tester input
        $this->commandTester->setInputs(['no']);

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // get command output
        $output = $this->commandTester->getDisplay();

        // assert result
        $this->assertStringContainsString('Clearing logs cancelled', $output);
        $this->assertEquals(Command::FAILURE, $exitCode);
    }

    /**
     * Test execute command when confirmation is accepted
     *
     * @return void
     */
    public function testExecuteCommandWhenConfirmationIsAccepted(): void
    {
        // mock get database name and entity table name
        $this->appUtil->method('getEnvValue')->with('DATABASE_NAME')->willReturn('test_database');
        $this->databaseManager->method('getEntityTableName')->with(Log::class)->willReturn('log_table');

        // set inputs for confirmation
        $this->commandTester->setInputs(['yes']);

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // get command output
        $output = $this->commandTester->getDisplay();

        // assert result
        $this->assertStringContainsString('Logs cleared successfully', $output);
        $this->assertEquals(Command::SUCCESS, $exitCode);
    }

    /**
     * Test execute command when exception is thrown
     *
     * @return void
     */
    public function testExecuteCommandWhenExceptionIsThrown(): void
    {
        // mock get database name and entity table name
        $this->appUtil->method('getEnvValue')->willReturn('test_database');
        $this->databaseManager->method('getEntityTableName')->willThrowException(
            new Exception('Database error')
        );

        // set inputs for confirmation
        $this->commandTester->setInputs(['yes']);

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // get command output
        $output = $this->commandTester->getDisplay();

        // assert result
        $this->assertStringContainsString('Process error: Database error', $output);
        $this->assertEquals(Command::FAILURE, $exitCode);
    }
}
