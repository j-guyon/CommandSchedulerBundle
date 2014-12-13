CommandSchedulerBundle
======================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8d984140-0e19-4c4f-8b05-605025eebeb5/big.png)](https://insight.sensiolabs.com/projects/8d984140-0e19-4c4f-8b05-605025eebeb5)

This bundle will allow you to easily manage scheduling for Symfony's console commands (native or not) with cron expression.

**Version**: 1.0-dev  
**Compatibility**: Symfony >= 2.3.0, Doctrine ORM

## Features

- An admin interface to add, edit, enable/disable or delete scheduled commands.
- For each command, you define : 
 - name
 - symfony console command (choice based on native `list` command)
 - cron expression (see [Cron format](http://en.wikipedia.org/wiki/Cron#Format) for informations)
 - output file 
 - priority
- A new console command `scheduler:execute [--dump]` which will be the single entry point to all commands
- Management of queuing and prioritization between tasks 
- Locking system, to stop scheduling a command that has returned an error
- Translated in french and english

## Screenshots
![list](https://raw.githubusercontent.com/J-Mose/CommandSchedulerBundle/master/Resources/doc/images/scheduled-list.png)

![new](https://raw.githubusercontent.com/J-Mose/CommandSchedulerBundle/master/Resources/doc/images/new-schedule.png)

![new2](https://raw.githubusercontent.com/J-Mose/CommandSchedulerBundle/master/Resources/doc/images/command-list.png)

## Documentation

See the [documentation here](Resources/doc/index.md).

##License

This bundle is under the MIT license. See the [complete license](Resources/meta/LICENCE) for info.
