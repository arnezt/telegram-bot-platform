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
use TelegramBotPlatform\Core\ConfigManager;
use TelegramBotPlatform\Core\UpdateHandler;
use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotPlatform\Tests\TelegramBotPlatformTest;

/**
 * Class UpdateHandlerTest
 * @package TelegramBotPlatform\Tests\Core
 */
class UpdateHandlerTest extends TestCase {

    public function testFoo() {

        $request = TelegramBotPlatformTest::getRequest(747719235, 59673324, '/cmd', 'bot_command');

        $configManager = new ConfigManager(TelegramBotPlatformTest::getConfig(), $request);

        $updateHandlerReflectionClass = new \ReflectionClass(UpdateHandler::class);
        $method = $updateHandlerReflectionClass->getMethod('identifyCommand');
        $method->setAccessible(true);

        $updateHandler = new UpdateHandler($configManager);
        $d = $method->invokeArgs($updateHandler, array('/start London, UK'));
        $s = $method->invokeArgs($updateHandler, array('/start@ApocalypseBot London, UK'));
        $f = $method->invokeArgs($updateHandler, array('/start@TBAPHPBot London, UK'));

        $this->assertEquals('start', $d);
        $this->assertEquals(null, $s);
        $this->assertEquals('start', $f);
    }

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
