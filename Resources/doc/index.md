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

*Note : use the last release, dev-master is not stable*

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

If you do not have auto_mapping set to true or you are using multiple entity managers, then set the bundle in the proper entity manager:
```yaml
# app/config/config.yml
doctrine:
    orm:
        entity_managers:
            default:
                mappings:
                    YourBundle: ~
                    JMoseCommandSchedulerBundle: ~
```

If you wish to use default texts provided in this bundle, you have to make sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: ~
```

For more information about translations, check [Symfony documentation](http://symfony.com/doc/current/book/translation.html).

Install bundle's assets :
``` bash
$ php app/console assets:install
```

Update your database 
``` bash
$ php app/console doctrine:schema:update --force
```

In case you're using another doctrine manager
``` bash
$ php app/console doctrine:schema:update --em=[manager_name] --force
```

Now, you can use the bundle and manage your scheduling here : **http://{you-app-root}/command-scheduler/list** and execute them with this new command
``` bash
$ php app/console scheduler:execute --dump
```

See the [Usage](#usage) section to have more information


### 4 - Available configuration

Here is the default bundle configuration.

```yaml
jmose_command_scheduler:

    # Default directory where scheduler will write output files
    #  This default value assume that php app/console is launched from project's root and that the directory is writable
    # if log_path is set to false, logging to files is disabled at all 
    log_path: app\logs\
    # This default value disables timeout checking (see monitoring), set to a numeric value (seconds) to enable it
    lock_timeout: false
    # receivers for reporting mails
    monitor_mail: []
    # to send "everything's all right" emails to receivers for reporting mails set this value to "true" (see monitoring)
    send_ok: false

    # Namespaces listed here won't be listed in the list
    excluded_command_namespaces:
        - _global
        - scheduler
        - server
        - container
        - config
        - generate
        - init
        - router

    # Doctrine manager
    doctrine_manager: default
```

Feel free to override it (especially `log_path`) in your `config.yml` file.


Usage
============

After a successful installation, you can access to this URL:

`http://{you-app-root}/command-scheduler/list`.

From this screen, you can do following actions :
  - Create a new scheduling
  - Edit an existing scheduling
  - Enable or disable scheduling (by clicking the "Power Off/On" switch)
  - Manually execute a command (It will be launched during the next `scheduler:execute`, regardless of the cron expression)
  - Unlock a task (if the lock is due to an unrecoverable error for example)

After that, you have to set (every few minutes, it depends of your needs) the following command in your system :
``` bash
$ php app/console scheduler:execute --env=env -vvv [--dump] [--no-output]
```

If the `--dump` option is set, the scheduler won't execute any command, but just list commands that should be executed.
Without the option, commands will be executed depending their priority and last execution time (highest priority will run first).

The `--env=` and `-v` (or `--verbosity`) arguments are passed to all scheduled command from `scheduler:execute`, so you don't have to put these on each scheduling !

If you don't want to have any message (except error) from scheduler itself you can use the `--no-output` option.

The `scheduler:execute` command will do following actions :
  - Get all scheduled commands in database (unlocked and enabled only)
  - Sort them by priority (desc)
  - Check if the command has to be executed at current time, based on its cron expression and on its last execution time
  - Execute eligible commands (without `exec` php function)


**Note** : Each command is locked just before his execution (and unlocked after).
This system avoid to have simultaneous process for the same command.
Thus, if an non-catchable error occurs, the command won't be executed again unless the problem is solved and the task is unlocked manually.

For any comments, questions, or bug report, use the  [Github issue tracker](https://github.com/J-Mose/CommandSchedulerBundle/issues).

Monitoring
=============

To enable (external) checks if the jobs are running correctly there is a URL which runs a check with the following requirements/limits:

 - only check enabled commands
 - return value not equal 0 means there is something wrong
 - running jobs can be checked for a maximum runtime

To run the check simply call

`http://{you-app-root}/command-scheduler/monitor`

The call returns a JSON object with either HTTP 200 and an empty array (everything ok) or HTTP 417 (Expectation failed) and an object containing all the (failed) jobs with name, last execution time, locked state and return code.

For "internal" monitoring of jobs there is also a command "scheduler:monitor" which does the same check as the monitor call before except it sends emails to an arbitrary number of receivers (if the server allows sending mails with the "mail" command).
As some kind of "self-monitoring" job the monitor command can be configured to send emails to all receivers if everything's ok - if there is no mail at all a problem occured.
