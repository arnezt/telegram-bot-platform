# Customization for Telegram Bot Platform

It happens that we need to redefine the **business logic**. <br />
Then we can change the **default handlers**.

Example custom `MyUpdateHandler`:

```php
<?php

namespace Acme\Handlers;


use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotPlatform\Api\TelegramUpdateHandlerInterface;

class MyUpdateHandler implements TelegramUpdateHandlerInterface {
    
    /**
     * {@inheritdoc}
     */
    public function runParser(TelegramBotPlatform $tbp) {
        
        $update = $tbp->getConfigManager()->getUpdate();
        
        // TODO: Implement parser update object and execute command.
    }
}
```

Example use custom `MyUpdateHandler`:

```php
<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array('vendor', 'autoload.php'));

$request = file_get_contents('php://input'); // Request body. (JSON-serialized Update object)
// ...

$tbp = new \TelegramBotPlatform\TelegramBotPlatform(array(
    'token'    => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11',
    'storage'  => $adapter
), $request);

$updateHandler = new \Acme\Handlers\MyUpdateHandler();

$tbp->setUpdateHandler($updateHandler);

$tbp->run();
```

Example custom `MyConfigManager`:

```php
<?php

namespace Acme\Managers;


use TelegramBotAPI\Types\Update;
use TelegramBotAPI\TelegramBotAPI;
use MatthiasMullie\Scrapbook\Psr16\SimpleCache;
use TelegramBotPlatform\Api\TelegramConfigManagerInterface;

class MyConfigManager implements TelegramConfigManagerInterface {
    
    /**
     * {@inheritdoc}
     */
    public function getTelegramBotAPI() {
        // TODO: Implement return TelegramBotAPI object.
    }
    
    /**
     * {@inheritdoc}
     */
    public function getStorage() {
        // TODO: Implement return SimpleCache object.
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDefaultCommand() {
        // TODO: return default class command.
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineQueryCommand() {
        // TODO: return class command inline query.
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands() {
        // TODO: return array commands where key is tag command and value command class or empty array.
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPayload() {
        // TODO: return Payload object or null.
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUpdate() {
        // TODO: Implement return Update object.
    }
                                        
    
    public function setConfig(array $config) {
        // TODO: Implement parser config
    }
     
    public function setRequest($request) {
        // TODO: Implement parser request
    }
}
```

Example use custom `MyConfigManager`:

```php
<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array('vendor', 'autoload.php'));

$request = file_get_contents('php://input'); // Request body. (JSON-serialized Update object)
// ...

$tbp = new \TelegramBotPlatform\TelegramBotPlatform();

$configManager = new \Acme\Managers\MyConfigManager();

$configManager->setConfig(array(
    'token'    => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11',
    'storage'  => $adapter
));
$configManager->setRequest($request);

$tbp->setConfigManager($configManager);

$tbp->run();
```
