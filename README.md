# ptlis/shell-command

A developer-friendly wrapper around execution of shell commands.

There were several requirements that inspired the creation of this package:
* Separation of running process state from command specification.
* Need for 'command specifications' that
    * Can be safely passed around the application before spawning a process.
    * Can spawn multiple concurrently-running processes.
    * Are stateless
* Desire for easily mockable interfaces & provision of default mocks.
* Integration of PSR-3 logging instrumentation.


[![Build Status](https://travis-ci.org/ptlis/shell-command.png?branch=master)](https://travis-ci.org/ptlis/shell-command) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/shell-command/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ptlis/shell-command/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/shell-command/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ptlis/shell-command/) [![Latest Stable Version](https://poser.pugx.org/ptlis/shell-command/v/stable.png)](https://packagist.org/packages/ptlis/shell-command)


## Install

Either from the console:

```shell
    $ composer require ptlis/shell-command:"~0.8"
```

Or by Editing composer.json:

```javascript
    {
        "require": {
            ...
            "ptlis/shell-command": "~0.8",
            ...
        }
    }
```

Followed by a composer update:

```shell
    $ composer update
```



## Usage

### The Builder

The package ships with a command builder, providing a simple and safe method to build commands. 

```php
    use ptlis\ShellCommand\CommandBuilder;
    
    $builder = new CommandBuilder();
```

The builder will attempt to determine your environment when constructed, you can override this by specifying an environment as the first argument:

```php

    use ptlis\ShellCommand\CommandBuilder;
    use ptlis\ShellCommand\UnixEnvironment;
    
    $builder = new CommandBuilder(new UnixEnvironment());
```

Note this builder is immutable - method calls must be chained and terminated with a call to ```buildCommand``` like so:
 
```php
    $command = $builder
        ->setCommand('foo')
        ->addArgument('--bar=baz')
        ->buildCommand()
``` 


#### Add Command

First we must provide the command to execute:

```php
    $builder->setCommand('git')             // Command in $PATH
        
    $builder->setCommand('./local/bin/git') // Relative to current working directory
        
    $builder->setCommand('/usr/bin/git')    // Fully qualified path to binary
```

If the command is not locatable a ```RuntimeException``` is thrown.


#### Set Timeout

Setting the timeout (in microseconds) sets how long the library will wait on a process before termination. Defaults to -1 which never forces termination.

```php
    $builder->setTimeout(30000000)          // Wait 30 seconds
```

If the process execution time exceeds this value a SIGTERM will be sent; if the process doesn't terminate after 1 second then a SIGKILL is sent.



#### Add Arguments

Next we may provide any arguments to the command, either chained:

```php
    $builder
        ->addArgument('--foo=bar')
        ->addArgument('-xzcf')
        ->addArgument('if=/dev/sda of=/dev/sdb')
```

Or in bulk:

```php
    $builder
        ->addArguments(array(
            '--foo=bar',
            '-xzcf',
            'if=/dev/sda of=/dev/sdb'
        ))
```



#### Run as sudo

Commands can be run with elevated privileges:

```php
    $builder
        ->setSudo(
            true,
            'password',
            'optional username'
        )
```



#### Set Environment Variables

Environment variables can be set when running a command:

```php
    $builder
        ->addEnvironmentVariable(
            'TEST_VARIABLE',
            '123'
        )
```



#### Add Process Observers

Finally we may attach any observers we wish to be executed by our running processes. In this case we add a simple logger:

```php
    $builder
        ->addProcessObserver(
            new AllLogger(
                new DiskLogger(),
                LogLevel::DEBUG
            )
        )
```


#### Build the Command

One the builder has been configured, the command can be retrieved for execution:

```php
    $command = $builder
        // Command configuration...
        ->buildCommand();
```



### Synchronous Execution

Executing the command is done using the ```runSynchronous``` method which returns an object implementing the ```CommandResultInterface```.

```php
    $result = $command->runSynchronous(); 
```

The exit code & output of the command are available as methods on this object:

```php
    $result->getExitCode();     // 0 for success, anything else conventionally indicates an error
    $result->getStdOut();       // The contents of stdout (as a string)
    $result->getStdOutLines();  // The contents of stdout (as an array of lines)
    $result->getStdErr();       // The contents of stderr (as a string)
    $result->getStdErrLines();  // The contents of stderr (as an array of lines)
```



## Mocking

Mock implementations of the Command & Builder interfaces are provided to aid testing.

By type hinting against the interfaces, rather than the concrete implementations, these mocks can be injected & used to return pre-configured result objects.


## Contributing

You can contribute by submitting an Issue to the [issue tracker](https://github.com/ptlis/shell-command/issues), improving the documentation or submitting a pull request. For pull requests i'd prefer that the code style and test coverage is maintained, but I am happy to work through any minor issues that may arise so that the request can be merged.




## Known limitations

* Currently supports UNIX environments only.
