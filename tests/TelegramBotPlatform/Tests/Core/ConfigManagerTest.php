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
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;

/**
 * Class ConfigManagerTest
 * @package TelegramBotShell\Tests\Core
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class ConfigManagerTest extends TestCase {

    /**
     * @return array
     */
    public function getConfig() {

        $mockBuilder = $this->getMockBuilder(TelegramBotCommandInterface::class);
        $mockBuilder = $mockBuilder->disableOriginalConstructor();
        $mockBuilder = $mockBuilder->setMethods(array('execute'));
        $mockCmd = $mockBuilder->getMock();

        $adapter = new MemoryStore();

        return array(
            'token'    => '479218867:AAGjGTwl0F-prMPIC6-AkNuLD1Bb2tRsYbc',
            'adapter'  => $adapter,
            'payload'  => null,
            'commands' => array(
                'default'  => get_class($mockCmd),
                'mappings' => array(
                    'help' => get_class($mockCmd),
                    'user' => get_class($mockCmd)
                )
            )
        );
    }

    /**
     * @return string
     */
    public function getRequest() {
        return '{
            "update_id": 747719235,
            "message": {
                "message_id": 1591,
                "from": {
                    "id": 59673324,
                    "is_bot": false,
                    "first_name": "Roma",
                    "last_name": "Baranenko",
                    "username": "roma_bb8",
                    "language_code": "ru"
                },
                "chat": {
                    "id": 59673324,
                    "first_name": "Roma",
                    "last_name": "Baranenko",
                    "username": "roma_bb8",
                    "type": "private"
                },
                "date": 1508587194,
                "text": "/cmd",
                "entities": [
                    {
                        "offset": 0,
                        "length": 4,
                        "type": "bot_command"
                    }
                ]
            }
        }';
    }


    /**
     * @expectedException \TelegramBotAPI\Exception\TelegramBotAPIException
     */
    public function testGetTelegramBotAPIEmpty() {

        $config = $this->getConfig();

        unset($config['token']);

        new ConfigManager($config, $this->getRequest());
    }

    public function testGetTelegramBotAPI() {

        $cm = new ConfigManager($this->getConfig(), $this->getRequest());

        $this->assertNotNull($cm->getTelegramBotAPI());
        $this->assertInstanceOf(TelegramBotAPI::class, $cm->getTelegramBotAPI());
    }


    /**
     * @expectedException \TelegramBotAPI\Exception\TelegramBotAPIException
     */
    public function testGetCacheEmpty() {

        $config = $this->getConfig();

        unset($config['adapter']);

        new ConfigManager($config, $this->getRequest());
    }

    public function testGetCache() {

        $cm = new ConfigManager($this->getConfig(), $this->getRequest());

        $this->assertNotNull($cm->getCache());
        $this->assertInstanceOf(SimpleCache::class, $cm->getCache());
    }


    public function testGetDefaultCommandEmpty() {

        $config = $this->getConfig();

        unset($config['commands']['default']);

        $cm = new ConfigManager($config, $this->getRequest());

        $this->assertNull($cm->getDefaultCommand());
    }

    public function testGetDefaultCommand() {

        $cm = new ConfigManager($this->getConfig(), $this->getRequest());

        $this->assertNotNull($cm->getDefaultCommand());

        $defaultCommand = $cm->getDefaultCommand();

        $cmd = new $defaultCommand();

        $this->assertInstanceOf(TelegramBotCommandInterface::class, $cmd);
    }


    public function testGetCommandsEmpty() {

        $config = $this->getConfig();

        unset($config['commands']);

        $cm = new ConfigManager($config, $this->getRequest());

        $this->assertNotNull($cm->getCommands());
        $this->assertEquals(array(), $cm->getCommands());
    }

    public function testGetCommands() {

        $cm = new ConfigManager($this->getConfig(), $this->getRequest());

        $this->assertNotNull($cm->getCommands());

        $commands = $cm->getCommands();

        $this->assertEquals(2, count($commands));
        $this->assertTrue(array_key_exists('help', $commands));
        $this->assertTrue(array_key_exists('user', $commands));
    }


    public function testGetPayloadEmpty() {

        $cm = new ConfigManager($this->getConfig(), $this->getRequest());

        $this->assertNull($cm->getPayload());
    }

    public function testGetPayload() {

        $config = $this->getConfig();

        $config['payload'] = 'hello world!';

        $cm = new ConfigManager($config, $this->getRequest());

        $this->assertNotNull($cm->getPayload());
        $this->assertEquals('hello world!', $cm->getPayload());
    }


    /**
     * @expectedException \TelegramBotAPI\Exception\TelegramBotAPIException
     */
    public function testGetUpdateEmpty() {
        new ConfigManager($this->getConfig(), null);
    }

    public function testGetUpdate() {

        $cm = new ConfigManager($this->getConfig(), $this->getRequest());

        $this->assertNotNull($cm->getUpdate());
        $this->assertInstanceOf(Update::class, $cm->getUpdate());

        $this->assertEquals(747719235, $cm->getUpdate()->getUpdateId());
    }
}
