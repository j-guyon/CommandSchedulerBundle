CommandSchedulerBundle
======================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8d984140-0e19-4c4f-8b05-605025eebeb5/mini.png)](https://insight.sensiolabs.com/projects/8d984140-0e19-4c4f-8b05-605025eebeb5)
[![Build Status](https://travis-ci.org/J-Mose/CommandSchedulerBundle.svg)](https://travis-ci.org/J-Mose/CommandSchedulerBundle)
[![Coverage Status](https://coveralls.io/repos/J-Mose/CommandSchedulerBundle/badge.svg)](https://coveralls.io/r/J-Mose/CommandSchedulerBundle)

This bundle will allow you to easily manage scheduling for Symfony's console commands (native or not) with cron expression.

**Version**: 1.0-dev
**Compatibility**: Symfony >= 2.3 to 2.7, Doctrine ORM

## Features

- An admin interface to add, edit, enable/disable or delete scheduled commands.
- For each command, you define : 
 - name
 - symfony console command (choice based on native `list` command)
 - cron expression (see [Cron format](http://en.wikipedia.org/wiki/Cron#Format) for informations)
 - output file 
 - priority
- A new console command `scheduler:execute [--dump] [--no-output]` which will be the single entry point to all commands
- Management of queuing and prioritization between tasks 
- Locking system, to stop scheduling a command that has returned an error
- Translated in french and english

## Screenshots
![list](Resources/doc/images/scheduled-list.png)

![new](Resources/doc/images/new-schedule.png)

![new2](Resources/doc/images/command-list.png)

## Documentation

See the [documentation here](Resources/doc/index.md).

##License

This bundle is under the MIT license. See the [complete license](Resources/meta/LICENCE) for info.
