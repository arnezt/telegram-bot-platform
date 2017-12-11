<p align="center">
  <img width="320" height="320" src="https://telegram.org/img/t_logo.png">
</p>

# Telegram Bot Shell

[![Travis CI](https://img.shields.io/travis/jungle-bay/telegram-bot-shell.svg?style=flat)](https://travis-ci.org/jungle-bay/telegram-bot-shell)
[![Scrutinizer CI](https://img.shields.io/scrutinizer/g/jungle-bay/telegram-bot-shell.svg?style=flat)](https://scrutinizer-ci.com/g/jungle-bay/telegram-bot-shell)
[![Codecov](https://img.shields.io/codecov/c/github/jungle-bay/telegram-bot-shell.svg?style=flat)](https://codecov.io/gh/jungle-bay/telegram-bot-shell)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/84f8c0b7-506d-4116-819c-f2080a79bf66.svg?style=flat)](https://insight.sensiolabs.com/projects/84f8c0b7-506d-4116-819c-f2080a79bf66)

This is platform for the telegram bot, based on pure [Telegram Bot API](https://github.com/jungle-bay/telegram-bot-api). <br />
Why [Shell](https://en.wikipedia.org/wiki/Unix_shell), I was inspired by the [command interpreter](https://en.wikipedia.org/wiki/Shell_(computing)) used in the [operating systems](https://en.wikipedia.org/wiki/Operating_system) of the [*nix](https://en.wikipedia.org/wiki/Unix-like) family.

### Install

The recommended way to install is through [Composer](https://getcomposer.org):

```bash
composer require jungle-bay/telegram-bot-shell
```

### The simplest example of use

```php
<?php

require_once(__DIR__ . '/vendor/autoload.php');

$request = file_get_contents('php://input');                    // Request body. (JSON-serialized Update object)

$tba = new \TelegramBotShell\TelegramBotShell(array(
    'token'    => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11',  // Your token bot.
    'adapter'  => $adapter,                                     // This adapter for Scrapbook library. See the complete: https://github.com/matthiasmullie/scrapbook#adapters
    'payload'  => $db,                                          // This payload will be passed to command the third parameter. (optional)
    'commands' => array(
        'default'  => \Acme\MyBot\Commands\DefaultCmd::class,   // This command will work by default if no command is found. (optional)
        'mappings' => array(                                    // This is the list of registered commands for the bot. (optional)
            'help' => \Acme\MyBot\Commands\HelpCmd::class,
            'user' => \Acme\MyBot\Commands\UserCmd::class
        )
    )
), $request);

$tba->run();
```

### Docs

See the complete docs in [here](https://github.com/jungle-bay/telegram-bot-shell/blob/master/docs/readme.md).

For the convenience of development, you can use [Telegram Bot CLI](https://github.com/jungle-bay/telegram-bot-cli).

### License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: [here](https://github.com/jungle-bay/telegram-bot-shell/blob/master/license.txt).
