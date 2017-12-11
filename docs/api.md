# API for Telegram Bot Shell

**Application Programming Interface very simple, it consists of four methods!**

### 1. getTelegramBotAPI

> This method has no parameters. <br />
> It return object of type [Telegram Bot API](https://github.com/jungle-bay/telegram-bot-api).

### 2. setContext

> This method has an array type. <br />
> Set the following context.

Example use:

```php
$tbs->setContext(array(
    'id'      => 123456,                                       // Chat id or User id or Unique Id. (integer|string)
    'context' => array(
        'command'   => \Acme\MyBot\Commands\UserCmd::class,    // Class the following commands.    (string)
        'method'    => 'thanks'                                // The method you need to run.      (string)
    )
));
```

### 3. deleteContext

> This method has one parameter that is **Chat id** or **User id**. <br />
> The method deletes the command context.

### 4. run

> This method has no parameters. <br />
> The method starts the command processing engine.
