CommandSchedulerBundle
======================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8d984140-0e19-4c4f-8b05-605025eebeb5/big.png)](https://insight.sensiolabs.com/projects/8d984140-0e19-4c4f-8b05-605025eebeb5)

This bundle will allow you to easily manage scheduling for Symfony's console commands (native or not) with cron expression.

**Version**: 1.0-dev  
**Compatibility**: Symfony >= 2.3.0, Doctrine



## Features

- An admin interface to add, edit, enable/disable or delete scheduled commands.
- For each command, you define : 
 - name
 - symfony console command (choice based on native `list` command)
 - cron expression
 - output file 
 - priority
- A new console command `scheduler:execute [--dump]` which will be the single entry point to all commands
- Management of queuing and prioritization between tasks 
- Management of lock
- Translated in french and english

## Screenshots
![list](https://raw.githubusercontent.com/J-Mose/CommandSchedulerBundle/master/Resources/doc/images/scheduled-list.png)

![new](https://raw.githubusercontent.com/J-Mose/CommandSchedulerBundle/master/Resources/doc/images/new-schedule.png)

![new2](https://raw.githubusercontent.com/J-Mose/CommandSchedulerBundle/master/Resources/doc/images/command-list.png)

## Documentation

See the documation [here](https://github.com/J-Mose/CommandSchedulerBundle/blob/master/Resources/doc/index.md).


##License

This bundle is under the MIT license. See the complete license in the bundle:

Resources/meta/LICENSE
