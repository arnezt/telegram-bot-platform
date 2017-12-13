<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 08.12.17
 * Time: 0:51
 */

namespace TelegramBotPlatform\Tests;


use PHPUnit\Framework\TestCase;
use TelegramBotPlatform\TelegramBotPlatform;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;

/**
 * Class TelegramBotPlatformTest
 * @package TelegramBotPlatformTest\Tests
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class TelegramBotPlatformTest extends TestCase {

    /**
     * @param int $updateId
     * @param int $chatId
     * @param string $text
     * @param string $type
     * @return string
     */
    public function getRequest($updateId, $chatId, $text, $type) {

        $request = '{
            "update_id": %d,
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
                    "id": %d,
                    "first_name": "Roma",
                    "last_name": "Baranenko",
                    "username": "roma_bb8",
                    "type": "private"
                },
                "date": 1508587194,
                "text": "%s",
                "entities": [
                    {
                        "offset": 0,
                        "length": %d,
                        "type": "%s"
                    }
                ]
            }
        }';

        return sprintf($request, $updateId, $chatId, $text, strlen($text), $type);
    }

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
            'storage'  => $adapter,
            'payload'  => null,
            'mappings' => array(
                'default'      => get_class($mockCmd),
                'inline_query' => get_class($mockCmd),
                'commands'     => array(
                    'help' => get_class($mockCmd),
                    'user' => get_class($mockCmd)
                )
            )
        );
    }


    /**
     * @return array
     */
    public function dataProvider() {

        $oneRequest = $this->getRequest(747719235, 59673324, '/cmd', 'bot_command');
        $twoRequest = $this->getRequest(747719236, 59673324, '/user', 'bot_command');
        $freeRequest = $this->getRequest(747719237, 59673324, 'Nike Cortez', 'text');
        $fourRequest = $this->getRequest(747719238, 59673324, 'ok!', 'text');

        return array(
            array($oneRequest),
            array($twoRequest),
            array($freeRequest),
            array($fourRequest)
        );
    }


    /**
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testGetConfigManager() {

        $request = $this->getRequest(747719238, 59673324, 'ok!', 'text');

        $tbp = new TelegramBotPlatform($this->getConfig(), $request);

        $this->assertNotNull($tbp->getConfigManager());
        $this->assertEquals(747719238, $tbp->getConfigManager()->getUpdate()->getUpdateId());
    }

    /**
     * @throws TelegramBotAPIException
     */
    public function testGetTelegramBotAPI() {

        $request = $this->getRequest(747719238, 59673324, 'ok!', 'text');
        $config = $this->getConfig();

        $tbp = new TelegramBotPlatform($this->getConfig(), $request);

        $this->assertNotNull($tbp->getTelegramBotAPI());
        $this->assertEquals($config['token'], $tbp->getTelegramBotAPI()->getToken());
    }

    /**
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testGetUpdateHandler() {

        $request = $this->getRequest(747719238, 59673324, 'ok!', 'text');

        $tbp = new TelegramBotPlatform($this->getConfig(), $request);

        $this->assertNotNull($tbp->getUpdateHandler());
    }

    /**
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testSetGetSession() {

        $request = $this->getRequest(747719238, 59673324, 'ok!', 'text');

        $tbp = new TelegramBotPlatform($this->getConfig(), $request);

        $tbp->setSession(array(
            'id'      => $tbp->getConfigManager()->getUpdate()->getMessage()->getChat()->getId(),
            'context' => array(
                'class'  => 'Cmd',
                'method' => 'execute'
            )
        ));

        $session = $tbp->getSession($tbp->getConfigManager()->getUpdate()->getMessage()->getChat()->getId());

        $this->assertNotNull($session);
        $this->assertEquals('Cmd', $session['class']);
        $this->assertEquals('execute', $session['method']);
    }

    /**
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testDeleteSession() {

        $request = $this->getRequest(747719238, 59673324, 'ok!', 'text');

        $tbp = new TelegramBotPlatform($this->getConfig(), $request);

        $tbp->deleteSession($tbp->getConfigManager()->getUpdate()->getMessage()->getChat()->getId());

        $session = $tbp->getSession($tbp->getConfigManager()->getUpdate()->getMessage()->getChat()->getId());

        $this->assertNull($session);
    }

    /**
     * @param string $request
     * @throws TelegramBotAPIException
     * @dataProvider dataProvider
     * @expectedException \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testRun($request) {

        $tbp = new TelegramBotPlatform($this->getConfig(), $request);

        $tbp->run();
    }
}
