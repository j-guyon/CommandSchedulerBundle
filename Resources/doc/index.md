Installation
============

## With Symfony Flex

Allow Flex to use contrib recipes and require the bundle :  
``` bash
$ composer config extra.symfony.allow-contrib true
$ composer require jmose/command-scheduler-bundle
```

The recipe will enable the bundle and its routes, so you can go directly to the [configuration section](#2---set-up-configuration)

## Without Symfony Flex

### 1 - Install the bundle

Add the bundle and dependencies in  your `composer.json` : 
``` bash
$ composer require jmose/command-scheduler-bundle
```

If you don't have composer yet, please refer to [the official Composer website](http://getcomposer.org/).


*Note : use the last release, dev-master is not stable*


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

Now, you have to register the routes provided by the bundle :  
```yaml
# app/config/routing.yml

jmose_command_scheduler:
    resource: "@JMoseCommandSchedulerBundle/Resources/config/routing.yml"
    prefix:   /
```

### 2 - Set up configuration

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
$ php bin/console assets:install
```

### 3 - Update the database 
``` bash
$ php bin/console doctrine:schema:update --force
```

In case you're using another doctrine manager
``` bash
$ php bin/console doctrine:schema:update --em=[manager_name] --force
```

Now, you can use the bundle and manage your scheduling here : **http://{your-app-root}/command-scheduler/list** and execute them with this new command
``` bash
$ php bin/console scheduler:execute
```

See the [Usage](#usage) section to have more information


### 4 - Available configuration

Here is the default bundle configuration. The flex recipe will create the scheduler.yaml file in your packages directory where you can change the base configuration.

```yaml
jmose_command_scheduler:

    # Default directory where scheduler will write output files
    #  This default value assume that php bin/console is launched from project's root and that the directory is writable
    # if log_path is set to false, logging to files is disabled at all 
    log_path: "%kernel.logs_dir%"
    # This default value disables timeout checking (see monitoring), set to a numeric value (seconds) to enable it
    lock_timeout: false
    # receivers for reporting mails
    monitor_mail: []
    # set a custom subject for monitor mails (first placeholder will be replaced by the hostname, second by the date)
    # double percentage is used to escape the percentage so they stay parameters
    monitor_mail_subject: cronjob monitoring %%s, %%s
    # to send "everything's all right" emails to receivers for reporting mails set this value to "true" (see monitoring)
    send_ok: false

    # Namespaces listed here won't be listed in the list
    excluded_command_namespaces:

    # Only namespaces listed here will be listed in the list. Not compatible together with excluded_command_namespaces.
    included_command_namespaces:

    # Doctrine manager
    doctrine_manager: default
```

Feel free to override it (especially `log_path`) in your `config.yml` file.

### 5 - Override the navigation bar

If you'd like to alter the navigation bar shown on `http://{your-app-root}/command-scheduler/list` you'll want to override the navbar template.
This can easily be done by using standard overrides in Symfony, as described [here](http://symfony.com/doc/current/templating/overriding.html).

In your project, you'll want to copy the `Navbar:navbar:html.twig` template into `app/Resources/JMoseCommandSchedulerBundle/views/Navbar/navbar.html.twig`.  Any changes to the file in this location will take precedence over the bundle's template file.

### 6 - EasyAdmin integration

If you want to manage your scheduled commands via [EasyAdmin](https://github.com/EasyCorp/EasyAdminBundle) here is a configuration template that you can copy/paste and change to your needs.
 
```yaml
easy_admin:
  entities:
    Cron:
      translation_domain: 'JMoseCommandScheduler'
      label: 'list.title'
      class: JMose\CommandSchedulerBundle\Entity\ScheduledCommand
      list:
        title: "list.title"
        fields:
          - { property: 'id', label: 'ID' }
          - { property: 'name', label: 'detail.name' }
          - { property: 'command', label: 'detail.command' }
          - { property: 'arguments', label: 'detail.arguments' }
          - { property: 'lastExecution', label: 'detail.lastExecution' }
          - { property: 'lastReturncode', label: 'detail.lastReturnCode' }
          - { property: 'locked', label: 'detail.locked', type: boolean}
          - { property: 'priority', label: 'detail.priority' }
          - { property: 'disabled', label: 'detail.disabled' }
        actions:
          - { name: 'jmose_command_scheduler_action_execute', type: 'route', label: 'action.execute' }
          - { name: 'jmose_command_scheduler_action_unlock', type: 'route', label: 'action.unlock' }
      form:
        fields:
          - { property: 'name', label: 'detail.name' }
          - { property: 'command', label: 'detail.command', type: 'JMose\CommandSchedulerBundle\Form\Type\CommandChoiceType' }
          - { property: 'arguments', label: 'detail.arguments' }
          - { property: 'cronExpression', label: 'detail.cronExpression' }
          - { property: 'priority', label: 'detail.priority' }
          - { property: 'disabled', label: 'detail.disabled' }
          - { property: 'logFile', label: 'detail.logFile' }
      new:
        fields:
          - { property: 'executeImmediately', label: 'detail.executeImmediately' }
```

Usage
============

After a successful installation, you can access to this URL:

`http://{your-app-root}/command-scheduler/list`.

From this screen, you can do following actions :
  - Create a new scheduling
  - Edit an existing scheduling
  - Enable or disable scheduling (by clicking the "Power Off/On" switch)
  - Manually execute a command (It will be launched during the next `scheduler:execute`, regardless of the cron expression)
  - Unlock a task (if the lock is due to an unrecoverable error for example)
  
When creating a new scheduling, you can provide your commands arguments and options exactly as you wold do from the console. Remember to use quotes when using arguments and options that includes white spaces.

After that, **you have to set (every few minutes, it depends of your needs) the following command in your system crontab** :
``` bash
$ php bin/console scheduler:execute --env=env -vvv [--dump] [--no-output]
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

Use dynamic parameters :

- %last_execution% : last execution date (format : Y-m-d H:i:s). Available for arguments and options 
- %log_file% : output file. Available for arguments and options
- %last_return_code% : last return code. ONLY available for options (possible value -1 is not working for Argument)

The `scheduler:unlock` command is capable of unlock all or a single scheduled command with a `lock-timeout` parameter.
It can be usefull if you don't have a full control about server restarting, which can a command in a lock state.

**Deamon (Beta)** : If you don't want to set up a cron job, you can use  `scheduler:start` and `scheduler:stop` commands.  
This commands manage a deamon process that will call `scheduler:execute` every minute. It require the `pcntl`php extension.  
Note that with this mode, if a command with an error, it will stop all the scheduler.

**Note** : Each command is locked just before his execution (and unlocked after).
This system avoid to have simultaneous process for the same command.
Thus, if an non-catchable error occurs, the command won't be executed again unless the problem is solved and the task is unlocked manually.

Monitoring
=============

To enable (external) checks if the jobs are running correctly there is a URL which runs a check with the following requirements/limits:

 - only check enabled commands
 - return value not equal 0 means there is something wrong
 - running jobs can be checked for a maximum runtime

To run the check simply call

`http://{your-app-root}/command-scheduler/monitor`

The call returns a JSON object with either HTTP 200 and an empty array (everything ok) or HTTP 417 (Expectation failed) and an object containing all the (failed) jobs with name, last execution time, locked state and return code.

For "internal" monitoring of jobs there is also a command "scheduler:monitor" which does the same check as the monitor call before except it sends emails to an arbitrary number of receivers (if the server allows sending mails with the "mail" command).
As some kind of "self-monitoring" job the monitor command can be configured to send emails to all receivers if everything's ok - if there is no mail at all a problem occured.


For any comments, questions, or bug report, use the  [Github issue tracker](https://github.com/J-Mose/CommandSchedulerBundle/issues).
