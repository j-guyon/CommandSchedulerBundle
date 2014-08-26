 
Installation
============


### 1 - Download the bundle
We will using the standard Symfony2 method here (composer).

Add `"jmose/command-scheduler-bundle": "dev-master"` in your composer.json:

```js
{
    "require": {
        "mtdowling/cron-expression": "1.*",
        "jmose/command-scheduler-bundle": "dev-master"
    }
}
```

Now download the bundle by running : 
``` bash
$ php composer.phar update jmose/command-scheduler-bundle
```

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

### 3 - Update configuration

Update your main `app/config/routing.yml` with these lines : 
```yaml
jmose_command_scheduler:
    resource: "@JMoseCommandSchedulerBundle/Resources/config/routing.yml"
    prefix:   /
```

If you use assetic, you have to register the bundle un your `app/config.yml` : 
```yaml
assetic:
    bundles: [ ... , JMoseCommandSchedulerBundle ]
```

Then, run these commands : 
``` bash
$ php app/console assets:install --env=dev
$ php app/console assetic:dump --env=dev
$ php app/console doctrine:schema:update --env=dev --force
```


Now, you can use the bundle and manage your scheduling here : **http://yourapp/command-scheduler/list** and execute them with this new command
``` bash
$ php app/console scheduler:execute --dump
```

If the `--dump` option is set, the scheduler won't execute any command, but just list commands that should be executed.
Without the option, commands will be execute regarding of their priority and last execution (highest priority will run first).

### 4 - Available configuration

Here is the default bundle configuration.

```yaml
parameters:

    # Default directory where scheduler will write output files
    #  This default value assume that php app/console is launched from project's root and that the directory is writable
    jmose_command_scheduler.log_path: app\logs\scheduler\

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

You will find the default configuration file  [here](https://github.com/J-Mose/CommandSchedulerBundle/blob/master/Resources/config/services.yml). 

Feel free to override it (especially `log_path`) in your app's parameters file.





