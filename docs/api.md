# API for Telegram Bot Platform

**Application Programming Interface very simple, it consists of four methods!**

### 1. getTelegramBotAPI

> This method has no parameters. <br />
> It return object of type [Telegram Bot API](https://github.com/jungle-bay/telegram-bot-api).

### 2. setSession

> This method has an array type. <br />
> Set the following context, return true if successful.

Example use:

```php
$tbp->setSession(array(
    'id'      => 123456,                                       // Chat id or User id or Unique Id. (string|integer)
    'context' => array(
        'class'     => \MyBot\Commands\UserCmd::class,         // Class the following commands.    (string)
        'method'    => 'thanks'                                // The method you need to run.      (string)
    )
));
```

### 3. deleteSession

> This method has one parameter that is **Chat id** or **User id**. or *Unique Id* <br />
> The method deletes the command context, return true if successful.

### 4. run

> This method has no parameters. <br />
> The method starts the command processing engine.
