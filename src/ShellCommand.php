<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2015 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ShellCommand;

use ptlis\ShellCommand\Interfaces\CommandInterface;

/**
 * Shell Command, encapsulates the data required to execute a shell command.
 *
 * @todo Use timeout!
 */
class ShellCommand implements CommandInterface
{
    /**
     * @var string The command to execute.
     */
    private $command;

    /**
     * @var string[] Array of arguments to pass with the command.
     */
    private $argumentList;

    /**
     * @var int (microseconds) How long to wait for a command to finish executing, -1 to wait indefinitely.
     */
    private $timeout;

    /**
     * @var int The amount of time in milliseconds to sleep for when polling for completion, defaults to 1/100 of a
     *  second.
     */
    private $pollTimeout;


    /**
     * Constructor
     *
     * @param string $command
     * @param string[] $argumentList
     * @param int $timeout
     * @param int $pollTimeout
     */
    public function __construct(
        $command,
        array $argumentList,
        $timeout = -1,
        $pollTimeout = 1000
    ) {
        $this->command = $command;
        $this->argumentList = $argumentList;
        $this->timeout = $timeout;
        $this->pollTimeout = $pollTimeout;
    }

    /**
     * {@inheritDoc}
     */
    public function runSynchronous()
    {
        $process = $this->runAsynchronous();

        $process->wait();

        $exitCode = intval($process->readOutput(UnixRunningProcess::EXITCODE));
        $stdOut = $process->readOutput(UnixRunningProcess::STDOUT);
        $stdErr = $process->readOutput(UnixRunningProcess::STDERR);

        return new ShellResult(
            $exitCode,
            $stdOut,
            $stdErr
        );
    }

    /**
     * {@inheritDoc}
     *
     * @todo This should not assume Unix!
     */
    public function runAsynchronous()
    {
        return new UnixRunningProcess(
            $this->__toString(),
            $this->pollTimeout
        );
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return array_reduce(
            $this->argumentList,
            function ($string, $argument) {
                return $string . ' ' . escapeshellarg($argument);
            },
            $this->command
        );
    }
}