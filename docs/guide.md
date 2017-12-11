# Guide for Telegram Bot Platform

**To begin with, we will implement a couple of commands.** <br />

The command needs to implement the `TelegramBotCommandInterface`, what to signal about correct operation it is necessary to return `true`.

Example default command:

> This command will work when TelegramBotPlatform will not find a command or context.

```php
<?php

namespace MyBot\Commands;


use TelegramBotAPI\Types\Update;
use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;

class DefaultCmd implements TelegramBotCommandInterface {

    /**
     * {@inheritdoc}
     */
    public function execute(TelegramBotPlatform $tbp, Update $update, $payload = null) {
        
        if (null === $update->getMessage()) return false;
        
        $tbp->getTelegramBotAPI()->sendMessage(array(
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text'    => 'Something I can not understand you: ('
        ));
        
        return true;
    }
}
```

Example help command:

> This command will fire when the user uses the `/help` command.

```php
<?php

namespace MyBot\Commands;


use TelegramBotAPI\Constants;
use TelegramBotAPI\Types\Update;
use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;

class HelpCmd implements TelegramBotCommandInterface {

    /**
     * @return string
     */
    public function aboutExecute() {
        return 'Displays this message.';
    }

    /**
     * {@inheritdoc}
     */
    public function execute(TelegramBotPlatform $tbp, Update $update, $payload = null) {

        $commands = $tbp->getConfigManager()->getCommands();
        $message = '';

        foreach ($commands as $tag => $class) {

            $command = new $class();

            $about = '';

            if (true === method_exists($command, 'aboutExecute')) $about = $command->aboutExecute();
            
            if ('' === $about) continue;
            
            $message .= '/' . $tag . ' - ' . $about . "\n";
        }

        $tbp->getTelegramBotAPI()->sendMessage(array(
            'chat_id'    => $update->getMessage()->getChat()->getId(),
            'text'       => $message,
            'parse_mode' => Constants::MARKDOWN_PARSE_MODE
        ));
        
        return true;
    }
}
```

Example user command:

> This command will fire when the user uses the `/user` command. <br />
> And it will work two more times in context.

```php
<?php

namespace MyBot\Commands;


use TelegramBotAPI\Types\Update;
use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;

class UserCmd implements TelegramBotCommandInterface {

    /**
     * @return string
     */
    public function aboutExecute() {
        return 'Command vivas.';
    }

    /**
     * @param TelegramBotPlatform $tbp
     * @param Update $update
     * @return bool  
     * @throws TelegramBotAPIException
     */
    public function thanks(TelegramBotPlatform $tbp, Update $update) {

        if (null === $update->getMessage()) return false;

        $user = $update->getMessage()->getFrom();

        $message = $tbp->getTelegramBotAPI()->sendMessage(array(
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text'    => 'Before meeting ' . $user->getFirstName() . ' ' . $user->getLastName() . ' !!!'
        ));

        $tbp->deleteSession($message->getChat()->getId());
        
        return true;
    }

    /**
     * @param TelegramBotPlatform $tbp
     * @param Update $update
     * @param mixed $payload
     * @return bool 
     * @throws TelegramBotAPIException
     */
    public function followingAnswer(TelegramBotPlatform $tbp, Update $update, $payload) {

        if (null === $update->getMessage()) return false;

        $pdo = $payload;

        $message = $tbp->getTelegramBotAPI()->sendMessage(array(
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text'    => 'This is ' . $update->getMessage()->getText() . ' not so bad;)'
        ));

        $tbp->setSession(array(
            'id'      => $message->getChat()->getId(),
            'context' => array(
                'class'   => self::class,
                'method'  => 'thanks'
            )
        ));
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(TelegramBotPlatform $tbp, Update $update, $payload = null) {

        $message = $tbp->getTelegramBotAPI()->sendMessage(array(
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text'    => 'Hello how are you?'
        ));

        $tbp->setSession(array(
            'id'      => $message->getChat()->getId(),
            'context' => array(
                'class'   => self::class,
                'method'  => 'followingAnswer'
            )
        ));
        
        return true;
    }
}
```

**To TelegramBotPlatform respond to the teams you need to register them!**

Example use:

> Now you need to create a TelegramBotPlatform object and pass it certain data! <br />
> And choose an adapter to store the context!

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$pdo = new \PDO("mysql:host={$host};port={$port};dbname={$dbname}", $user, $password);

// $adapter = new \MatthiasMullie\Scrapbook\Adapters\Memcached(%SERVER_MEMCACHED%);
// $adapter = new \MatthiasMullie\Scrapbook\Adapters\Apc();
// ***
$adapter = new \MatthiasMullie\Scrapbook\Adapters\MySQL($pdo);

$request = file_get_contents('php://input');

$tbp = new \TelegramBotPlatform\TelegramBotPlatform(array(
    'token'    => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11',              // Your token bot.
    'storage'  => $adapter,                                                 // This adapter for Scrapbook library to store user sessions. See the complete adapters: https://github.com/matthiasmullie/scrapbook#adapters
    'payload'  => $pdo,                                                     // This payload will be passed for third parameter to command. (optional)
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

**Happy coding and cool bots!**
