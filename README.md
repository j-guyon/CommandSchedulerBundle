# This is only a development fork. Please refer to [original Bundle](https://github.com/J-Mose/CommandSchedulerBundle) if you want to use this bundle.


CommandSchedulerBundle
======================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f0991948-dd04-4f4d-a6a4-1a348d1ceb57/mini.png)](https://insight.sensiolabs.com/projects/f0991948-dd04-4f4d-a6a4-1a348d1ceb57)
[![Build Status](https://travis-ci.org/homerjsimpson000/CommandSchedulerBundle.svg)](https://travis-ci.org/homerjsimpson000/CommandSchedulerBundle)
[![Coverage Status](https://coveralls.io/repos/homerjsimpson000/CommandSchedulerBundle/badge.svg?branch=master&service=github)](https://coveralls.io/github/homerjsimpson000/CommandSchedulerBundle?branch=master)

This bundle will allow you to easily manage scheduling for Symfony's console commands (native or not) with cron expression.

**Version**: 1.1.0  
**Compatibility**:  
 - Symfony 2.8 to 3.0
 - PHP 5.3 >= 7.0
 - Doctrine ORM

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
- Monitoring with timeout or failed commands (Json URL and command with mailing)
- Translated in french and english

## Screenshots
![list](Resources/doc/images/scheduled-list.png)

![new](Resources/doc/images/new-schedule.png)

![new2](Resources/doc/images/command-list.png)

## Documentation

See the [documentation here](Resources/doc/index.md).

##License

This bundle is under the MIT license. See the [complete license](Resources/meta/LICENCE) for info.
