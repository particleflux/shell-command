<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ShellCommand\Test\Integration\Logger;

use Psr\Log\LogLevel;
use ptlis\ShellCommand\Interfaces\RunningProcessInterface;
use ptlis\ShellCommand\Logger\SignalSentLogger;
use ptlis\ShellCommand\Test\MockPsrLogger;
use ptlis\ShellCommand\Test\ptlisShellCommandTestcase;
use ptlis\ShellCommand\UnixEnvironment;
use ptlis\ShellCommand\UnixRunningProcess;

class UnixSignalSentLoggerTest extends ptlisShellCommandTestcase
{
    /**
     * Note - this test may (in theory) fail - being timer-dependant there's a chance that the process may exit early.
     */
    public function testCalled()
    {
        $this->skipIfNotUnix();

        $command = './tests/commands/unix/sleep_binary';

        $mockLogger = new MockPsrLogger();

        $process = new UnixRunningProcess(
            new UnixEnvironment(),
            $command,
            getcwd(),
            -1,
            1000,
            new SignalSentLogger(
                $mockLogger,
                LogLevel::DEBUG
            )
        );
        $process->stop();

        $this->assertLogsMatch(
            array(
                array(
                    'level' => LogLevel::DEBUG,
                    'message' => 'Signal sent',
                    'context' => array(
                        'signal' => RunningProcessInterface::SIGTERM
                    )
                )
            ),
            $mockLogger->getLogs()
        );
    }
}