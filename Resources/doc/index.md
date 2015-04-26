Installation
============

### 1 - Install the bundle
We will be using the standard Symfony method here (composer).

Add the bundle and dependencies in  your `composer.json` : 
``` bash
$ php composer.phar require jmose/command-scheduler-bundle
```

If you don't have composer yet, please refer to [the official Composer website](http://getcomposer.org/).

Composer will install the bundle to your project's `vendor` directory.

### 2 - Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new JMose\CommandSchedulerBundle\JMoseCommandSchedulerBundle(),
    );
}
```

### 3 - Set up configuration

First, you have to register the routes provided by the bundle :  
```yaml
# app/config/routing.yml

jmose_command_scheduler:
    resource: "@JMoseCommandSchedulerBundle/Resources/config/routing.yml"
    prefix:   /
```

If you wish to use default texts provided in this bundle, you have to make sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: ~
```

For more information about translations, check [Symfony documentation](http://symfony.com/doc/current/book/translation.html).

Run assetic commands : 
``` bash
$ php app/console assets:install
$ php app/console assetic:dump
```

Update your database 
``` bash
$ php app/console doctrine:schema:update --force
```

Now, you can use the bundle and manage your scheduling here : **http://{you-app-root}/command-scheduler/list** and execute them with this new command
``` bash
$ php app/console scheduler:execute --dump
```

See the [Usage](#usage) section to have more informations


### 4 - Available configuration

Here is the default bundle configuration.

```yaml
parameters:

    # Default directory where scheduler will write output files
    #  This default value assume that php app/console is launched from project's root and that the directory is writable
    jmose_command_scheduler.log_path: app\logs\

    # Namespaces listed here won't be listed in the list
    jmose_command_scheduler.command_choice_list.excluded_namespaces:
        - _global
        - scheduler
        - server
        - container
        - config
        - generate
        - init
        - router
```

You will find the default configuration file  [here](../config/services.yml). 

Feel free to override it (especially `log_path`) in your app's parameters file.

 
Usage
============

After a succesfull installation, you can access to this URL: 

`http://{you-app-root}/command-scheduler/list`. 

From this screen, you can do following actions : 
  - Create a new scheduling
  - Edit an existing scheduling
  - Enable or disable scheduling (by clicking the "Power Off/On" switch)
  - Manualy execute a command (It will be launched during the next `scheduler:execute`, regardless of the cron expression)
  - Unlock a task (if the lock is due to an uncoverable error for example)

After that, you have to set (every few minutes, it depends of your needs) the following command in your system : 
``` bash
$ php app/console scheduler:execute --env=env -vvv [--dump]
```

If the `--dump` option is set, the scheduler won't execute any command, but just list commands that should be executed.
Without the option, commands will be executed depending their priority and last execution time (highest priority will run first).

The `--env=` and `-v` (or `--verbosity`) arguments are passed to all scheduled command from `scheduler:execute`, so you don't have to put these on each scheduling !

The `scheduler:execute` command will do following actions : 
  - Get all scheduled commands in database (unlocked and enabled only)
  - Sort them by priority (desc)
  - Check if the command has to be executed at current time, based on its cron expression and on its last execution time
  - Execute eligible commands (without `exec` php fonction)
 
  
**Note** : Each command is locked just before his execution (and unlocked after). 
This system avoid to have simultaneous process for the same command. 
Thus, if an non-catchable error occurs, the command won't be executed again unless the problem is solved and the task is unlocked manualy.

For any comments, questions, or bug report, use the  [Github issue tracker](https://github.com/J-Mose/CommandSchedulerBundle/issues).
