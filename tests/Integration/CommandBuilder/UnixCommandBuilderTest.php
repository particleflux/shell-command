<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2015 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ShellCommand\Test\Integration\CommandBuilder;

use ptlis\ShellCommand\CommandBuilder;
use ptlis\ShellCommand\Test\ptlisShellCommandTestcase;
use ptlis\ShellCommand\UnixEnvironment;

class UnixCommandBuilderTest extends ptlisShellCommandTestcase
{
    public function testInvalidCommand()
    {
        $this->skipIfNotUnix();

        $this->setExpectedException(
            '\RuntimeException',
            'Invalid command "foobar" provided to ptlis\ShellCommand\CommandBuilder.'
        );

        $builder = new CommandBuilder(new UnixEnvironment());

        $builder
            ->setCommand('foobar')
            ->buildCommand();
    }
}