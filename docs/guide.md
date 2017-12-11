# Guide for Telegram Bot Shell

**To begin with, we will implement a couple of commands.** <br />

The command needs to implement the ```TelegramBotCommandInterface```, what to signal about correct operation it is necessary to return ```true```.

Example default command:

> This command will work when TelegramBotShell will not find a command or context.

```php
<?php

namespace Acme\MyBot\Commands;


use TelegramBotAPI\Types\Update;
use TelegramBotShell\TelegramBotShell;
use TelegramBotShell\Api\TelegramBotCommandInterface;

class DefaultCmd implements TelegramBotCommandInterface {

    /**
     * {@inheritdoc}
     */
    public function execute(TelegramBotShell $tbs, Update $update, $payload = null) {
        
        if (null === $update->getMessage()) return false;
        
        $tbs->getTelegramBotAPI()->sendMessage(array(
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text'    => 'Something I can not understand you: ('
        ));
        
        return true;
    }
}
```

Example help command:

> This command will fire when the user uses the ```/help``` command.

```php
<?php

namespace Acme\MyBot\Commands;


use TelegramBotAPI\Constants;
use TelegramBotAPI\Types\Update;
use TelegramBotShell\TelegramBotShell;
use TelegramBotShell\Api\TelegramBotCommandInterface;

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
    public function execute(TelegramBotShell $tbs, Update $update, $payload = null) {

        $commands = $tbs->getConfigManager()->getCommands();
        $message = '';

        foreach ($commands as $tag => $command) {

            $cmd = new $command();

            $about = '';

            if (true === method_exists($cmd, 'aboutExecute')) $about = $cmd->aboutExecute();
            
            if ('' === $about) continue;
            
            $message .= '/' . $tag . ' - ' . $about . "\n";
        }

        $tba = $tbs->getTelegramBotAPI();

        $tba->sendMessage(array(
            'chat_id'    => $update->getMessage()->getChat()->getId(),
            'text'       => $message,
            'parse_mode' => Constants::MARKDOWN_PARSE_MODE
        ));
        
        return true;
    }
}
```

Example user command:

> This command will fire when the user uses the ```/user``` command. <br />
> And it will work two more times in context.

```php
<?php

namespace Acme\MyBot\Commands;


use TelegramBotAPI\Types\Update;
use TelegramBotShell\TelegramBotShell;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotShell\Api\TelegramBotCommandInterface;

class UserCmd implements TelegramBotCommandInterface {

    /**
     * @return string
     */
    public function aboutExecute() {
        return 'Command vivas.';
    }

    /**
     * @param TelegramBotShell $tbs
     * @param Update $update
     * @throws TelegramBotAPIException
     * @return bool  
     */
    public function thanks(TelegramBotShell $tbs, Update $update) {

        if (null === $update->getMessage()) return false;

        $user = $update->getMessage()->getFrom();

        $message = $tbs->getTelegramBotAPI()->sendMessage(array(
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text'    => 'Before meeting ' . $user->getFirstName() . ' ' . $user->getLastName() . ' !!!'
        ));

        $tbs->deleteContext($message->getChat()->getId());
        
        return true;
    }

    /**
     * @param TelegramBotShell $tbs
     * @param Update $update
     * @param mixed $payload
     * @throws TelegramBotAPIException
     * @return bool 
     */
    public function followingAnswer(TelegramBotShell $tbs, Update $update, $payload) {

        if (null === $update->getMessage()) return false;

        $pdo = $payload;

        $message = $tbs->getTelegramBotAPI()->sendMessage(array(
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text'    => 'This is ' . $update->getMessage()->getText() . ' not so bad;)'
        ));

        $tbs->setContext(array(
            'id'      => $message->getChat()->getId(),
            'context' => array(
                'command'   => self::class,
                'method'    => 'thanks'
            )
        ));
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(TelegramBotShell $tbs, Update $update, $payload = null) {

        $message = $tbs->getTelegramBotAPI()->sendMessage(array(
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text'    => 'Hello how are you?'
        ));

        $tbs->setContext(array(
            'id'      => $message->getChat()->getId(),
            'context' => array(
                'command'   => self::class,
                'method'    => 'followingAnswer'
            )
        ));
        
        return true;
    }
}
```

**To TelegramBotShell respond to the teams you need to register them!**

Example use:

> Now you need to create a TelegramBotShell object and pass it certain data! <br />
> And choose an adapter to store the context!

```php
<?php

require_once(__DIR__ . '/vendor/autoload.php');

$request = file_get_contents('php://input');

$pdo = new \PDO("mysql:host={$host};port={$port};dbname={$dbname}", $user, $password);

// $adapter = new \MatthiasMullie\Scrapbook\Adapters\Memcached(%SERVER_MEMCACHED%);
// $adapter = new \MatthiasMullie\Scrapbook\Adapters\Apc();
// ***
$adapter = new \MatthiasMullie\Scrapbook\Adapters\MySQL($pdo);

$tba = new \TelegramBotShell\TelegramBotShell(array(
    'token'    => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11',  // Your token bot.
    'adapter'  => $adapter,                                     // This adapter for Scrapbook library. See the complete: https://github.com/matthiasmullie/scrapbook#adapters
    'payload'  => $pdo,                                         // This payload will be passed to command the third parameter. (optional)
    'commands' => array(
        'default'  => \Acme\MyBot\Commands\DefaultCmd::class,   // This command will work by default if no command is found. (optional)
        'mappings' => array(                                    // This is the list of registered commands for the bot. (optional)
            'help' => \Acme\MyBot\Commands\HelpCmd::class,
            'user' => \Acme\MyBot\Commands\UserCmd::class
        )
    )
), $request);

$tbs->run();
```

**Happy coding and cool bots!**
