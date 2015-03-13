<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2015 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ShellCommand\Interfaces;

/**
 * Interface class that operating-system specific classes must implement.
 */
interface EnvironmentInterface
{
    /**
     * Accepts a command (without arguments) and verifies whether or not it can be executed.
     *
     * @param string $command
     * @param string $cwdOverride
     *
     * @return bool
     */
    public function validateCommand($command, $cwdOverride = '');

    /**
     * Accepts a command and a polling timeout and returns an object implementing RunningProcessInterface.
     *
     * @param CommandInterface $command
     * @param string $cwd
     * @param ProcessObserverInterface|null $processObserver
     * @param int $timeout
     * @param int $pollTimeout
     *
     * @return RunningProcessInterface
     */
    public function buildProcess(
        CommandInterface $command,
        $cwd,
        ProcessObserverInterface $processObserver = null,
        $timeout = -1,
        $pollTimeout = 1000
    );
}
