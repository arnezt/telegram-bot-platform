<p align="center">
    <a href="https://github.com/jungle-bay/telegram-bot-platform">
        <img width="128" height="128" src="logo.png" alt="Telegram Bot Platform Logo">
    </a>
</p>

# Telegram Bot Platform

[![Travis CI](https://img.shields.io/travis/jungle-bay/telegram-bot-platform.svg?style=flat)](https://travis-ci.org/jungle-bay/telegram-bot-platform)
[![Scrutinizer CI](https://img.shields.io/scrutinizer/g/jungle-bay/telegram-bot-platform.svg?style=flat)](https://scrutinizer-ci.com/g/jungle-bay/telegram-bot-platform)
[![Codecov](https://img.shields.io/codecov/c/github/jungle-bay/telegram-bot-platform.svg?style=flat)](https://codecov.io/gh/jungle-bay/telegram-bot-platform)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/4b05f9bd-f0ff-4423-8d53-2e5817bbd648.svg?style=flat)](https://insight.sensiolabs.com/projects/4b05f9bd-f0ff-4423-8d53-2e5817bbd648)

https://insight.sensiolabs.com/projects//big.png

This is PHP Library for [Telegram Bot Platform](https://telegram.org/blog/bot-revolution). <br />
You can follow [this](https://github.com/jungle-bay/telegram-bot-platform/blob/master/docs/readme.md) documentation to work with the library.

### Install

The recommended way to install is through [Composer](https://getcomposer.org/doc/00-intro.md#introduction):

```bash
composer require jungle-bay/telegram-bot-shell
```

### The simplest example of use

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$request = file_get_contents('php://input');                                // Request body. (JSON-serialized Update object)

$tbp = new \TelegramBotPlatform\TelegramBotPlatform(array(
    'token'    => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11',              // Your token bot.
    'storage'  => $adapter,                                                 // This adapter for Scrapbook library to store user sessions. See the complete adapters: https://github.com/matthiasmullie/scrapbook#adapters
    'payload'  => null,                                                     // This payload will be passed for third parameter to command. (optional)
    'mappings' => array(
        'default'       => \MyBot\Commands\DefaultCmd::class,               // This command will work by default if no command is found or user session. (optional)
        'inline_query'  => \MyBot\Commands\FeedbackInlineQueryCmd::class,   // This command will work with inline queries. (optional)
        'commands'      => array(                                           // This is the list of registered commands for the bot. (optional)
            'help' => \MyBot\Commands\HelpCmd::class,
            'user' => \MyBot\Commands\UserCmd::class
        )
    )
), $request);

$tbp->run();
```

### Docs

See the complete docs in [here](https://github.com/jungle-bay/telegram-bot-platform/blob/master/docs/readme.md).

### Note

For the convenience of development, you can use [Telegram Bot CLI](https://github.com/jungle-bay/telegram-bot-cli).

### License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: [here](https://github.com/jungle-bay/telegram-bot-platform/blob/master/license.txt).
