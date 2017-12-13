<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 13.12.17
 * Time: 22:20
 */

namespace TelegramBotPlatform\Tests\Core;


use PHPUnit\Framework\TestCase;
use TelegramBotPlatform\Core\UpdateHandler;
use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotPlatform\Tests\TelegramBotPlatformTest;

/**
 * Class UpdateHandlerTest
 * @package TelegramBotPlatform\Tests\Core
 */
class UpdateHandlerTest extends TestCase {

    /**
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testSetGetConfigManager() {

        $request = TelegramBotPlatformTest::getRequest(747719235, 59673324, '/cmd', 'bot_command');

        $tbp = new TelegramBotPlatform(TelegramBotPlatformTest::getConfig(), $request);

        $updateHandler = new UpdateHandler($tbp->getConfigManager());

        $updateHandler->setConfigManager($tbp->getConfigManager());

        $this->assertNotNull($updateHandler->getConfigManager());
        $this->assertEquals($tbp->getConfigManager(), $updateHandler->getConfigManager());
    }

    /**
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     * @throws \TelegramBotAPI\Exception\TelegramBotAPIException
     */
    public function testRunParser() {

        $config = TelegramBotPlatformTest::getConfig();

        $twoRequest = TelegramBotPlatformTest::getRequest(747719236, 59673324, '/test', 'bot_command');

        $tbp = new TelegramBotPlatform($config, $twoRequest);

        $tbp->run();

        $treeRequest = TelegramBotPlatformTest::getRequest(747719237, 59673324, 'asd', 'text');

        $tbp = new TelegramBotPlatform($config, $treeRequest);

        $tbp->run();
    }
}
