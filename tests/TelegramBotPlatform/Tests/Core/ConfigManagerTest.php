<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 08.12.17
 * Time: 0:51
 */

namespace TelegramBotPlatform\Tests\Core;


use PHPUnit\Framework\TestCase;
use TelegramBotAPI\Types\Update;
use TelegramBotAPI\TelegramBotAPI;
use TelegramBotPlatform\Core\ConfigManager;
use MatthiasMullie\Scrapbook\Psr16\SimpleCache;
use TelegramBotPlatform\Tests\TelegramBotPlatformTest;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;

/**
 * Class ConfigManagerTest
 * @package TelegramBotPlatform\Tests\Core
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class ConfigManagerTest extends TestCase {

    /**
     * @return array
     */
    public function dataProvider() {

        $request = TelegramBotPlatformTest::getRequest(747719235, 59673324, '/cmd', 'bot_command');

        return array(
            array($request)
        );
    }


    /**
     * @param $request
     * @dataProvider dataProvider
     * @expectedException \TelegramBotAPI\Exception\TelegramBotAPIException
     */
    public function testGetTelegramBotAPIEmpty($request) {

        $config = TelegramBotPlatformTest::getConfig();

        unset($config['token']);

        new ConfigManager($config, $request);
    }

    /**
     * @param $request
     * @dataProvider dataProvider
     */
    public function testGetTelegramBotAPI($request) {

        $cm = new ConfigManager(TelegramBotPlatformTest::getConfig(), $request);

        $this->assertNotNull($cm->getTelegramBotAPI());
        $this->assertInstanceOf(TelegramBotAPI::class, $cm->getTelegramBotAPI());
    }


    /**
     * @param $request
     * @dataProvider dataProvider
     * @expectedException \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testGetStorageEmpty($request) {

        $config = TelegramBotPlatformTest::getConfig();

        unset($config['storage']);

        new ConfigManager($config, $request);
    }

    /**
     * @param $request
     * @dataProvider dataProvider
     */
    public function testGetStorage($request) {

        $cm = new ConfigManager(TelegramBotPlatformTest::getConfig(), $request);

        $this->assertNotNull($cm->getStorage());
        $this->assertInstanceOf(SimpleCache::class, $cm->getStorage());
    }


    /**
     * @param $request
     * @dataProvider dataProvider
     */
    public function testGetDefaultCommandEmpty($request) {

        $config = TelegramBotPlatformTest::getConfig();

        unset($config['mappings']['default']);

        $cm = new ConfigManager($config, $request);

        $this->assertNull($cm->getDefaultCommand());
    }

    /**
     * @param $request
     * @dataProvider dataProvider
     */
    public function testGetDefaultCommand($request) {

        $cm = new ConfigManager(TelegramBotPlatformTest::getConfig(), $request);

        $this->assertNotNull($cm->getDefaultCommand());

        $defaultCommand = $cm->getDefaultCommand();

        $cmd = new $defaultCommand();

        $this->assertInstanceOf(TelegramBotCommandInterface::class, $cmd);
    }


    /**
     * @param $request
     * @dataProvider dataProvider
     */
    public function testGetCommandsEmpty($request) {

        $config = TelegramBotPlatformTest::getConfig();

        unset($config['mappings']);

        $cm = new ConfigManager($config, $request);

        $this->assertNotNull($cm->getCommands());
        $this->assertEquals(array(), $cm->getCommands());
    }

    /**
     * @param $request
     * @dataProvider dataProvider
     */
    public function testGetCommands($request) {

        $cm = new ConfigManager(TelegramBotPlatformTest::getConfig(), $request);

        $this->assertNotNull($cm->getCommands());

        $commands = $cm->getCommands();

        $this->assertEquals(1, count($commands));
        $this->assertTrue(array_key_exists('test', $commands));
    }


    /**
     * @param $request
     * @dataProvider dataProvider
     */
    public function testGetPayloadEmpty($request) {

        $cm = new ConfigManager(TelegramBotPlatformTest::getConfig(), $request);

        $this->assertNull($cm->getPayload());
    }

    /**
     * @param $request
     * @dataProvider dataProvider
     */
    public function testGetPayload($request) {

        $config = TelegramBotPlatformTest::getConfig();

        $config['payload'] = 'hello world!';

        $cm = new ConfigManager($config, $request);

        $this->assertNotNull($cm->getPayload());
        $this->assertEquals('hello world!', $cm->getPayload());
    }


    /**
     * @expectedException \TelegramBotAPI\Exception\TelegramBotAPIException
     */
    public function testGetUpdateEmpty() {
        new ConfigManager(TelegramBotPlatformTest::getConfig(), null);
    }

    /**
     * @param $request
     * @dataProvider dataProvider
     */
    public function testGetUpdate($request) {

        $cm = new ConfigManager(TelegramBotPlatformTest::getConfig(), $request);

        $this->assertNotNull($cm->getUpdate());
        $this->assertInstanceOf(Update::class, $cm->getUpdate());

        $this->assertEquals(747719235, $cm->getUpdate()->getUpdateId());
    }
}
