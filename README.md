CommandSchedulerBundle
======================

This Symfony commands scheduler will allow you to easily manage scheduling for Symfony's console commands (native or not) with cron expression.

**Version**: 1.0-dev  
**Compatibility**: Symfony >= 2.3.0, Doctrine


## Features

- An admin interface to add, edit, enable/disable or delete scheduled commands.
- For each command, you define : 
 - a name
 - a symfony console command (choice based on native `list` command)
 - a cron expression
 - an output file 
 - a priority (which will define the order of execution)
- A new console command "scheduler:execute (--dump)" which will be the single entry point to all commands and will launch them one after another, with a lock system to avoid multiple execution.
- Translations in french and english

## Screenshots
![list](https://raw.githubusercontent.com/J-Mose/CommandSchedulerBundle/master/Resources/doc/images/scheduled-list.png)

![new](https://raw.githubusercontent.com/J-Mose/CommandSchedulerBundle/master/Resources/doc/images/new-schedule.png)

![new2](https://raw.githubusercontent.com/J-Mose/CommandSchedulerBundle/master/Resources/doc/images/command-list.png)

## Documentation

See the documation [here](https://github.com/J-Mose/CommandSchedulerBundle/blob/master/Resources/doc/index.md).

