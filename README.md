# Background Process plugin for [PHPCI](https://www.phptesting.org)

This plugin allows you to manage daemons and other processes that should run for the duration
of your tests. Such as mailcatcher or beanstalk.

### Install the Plugin

1. Navigate to your PHPCI root directory and run `composer require cooperaj/phpci-process-plugin`
2. If you are using the PHPCI daemon, restart it
3. Update your `phpci.yml` in the project you want to deploy with

### PHPCI Config

```yml
setup:
    Cooperaj\PHPCI\Plugin\BackgroundProcesses:
        - "mailcatcher --ip 0.0.0.0 --foreground"
        - "someotherdaemon --always-run-in-foreground"
        - "SOME_ENVIRONMENT_VARIABLE=Some_value some_script with --parameter=value"
```

If you're using the PHPCI daemon to run your builds you'll also need to run the Stop plugin
as a part of your complete step otherwise the background processes you've started will not 
stop.

```yml
complete:
    Cooperaj\PHPCI\Plugin\StopBackgroundProcesses:
```
