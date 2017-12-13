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
use TelegramBotPlatform\Exception\TelegramBotPlatformException;
use TelegramBotPlatform\TelegramBotPlatform;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use TelegramBotPlatform\Tests\Stubs\TestCmdTestStub;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotPlatform\Tests\Stubs\DefaultCmdTestStub;
use TelegramBotPlatform\Tests\Stubs\InlineQueryCmdTestStub;

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
    public static function getRequest($updateId, $chatId, $text, $type) {

        $request = '{
            "update_id": %d,
            "message": {
                "message_id": 1591,
                "from": {
                    "id": %d,
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

        return sprintf($request, $updateId, $chatId, $chatId, $text, strlen($text), $type);
    }

    /**
     * @return array
     */
    public static function getConfig() {

        $adapter = new MemoryStore();

        return array(
            'token'    => '479218867:AAGjGTwl0F-prMPIC6-AkNuLD1Bb2tRsYbc',
            'storage'  => $adapter,
            'payload'  => null,
            'mappings' => array(
                'default'      => DefaultCmdTestStub::class,
                'inline_query' => InlineQueryCmdTestStub::class,
                'commands'     => array(
                    'test' => TestCmdTestStub::class
                )
            )
        );
    }


    /**
     * @return array
     */
    public function dataProvider() {

        $oneRequest = self::getRequest(747719235, 59673324, '/cmd', 'bot_command');
        $twoRequest = self::getRequest(747719236, 59673324, '/user', 'bot_command');
        $freeRequest = self::getRequest(747719237, 59673324, 'Nike Cortez', 'text');
        $fourRequest = self::getRequest(747719238, 59673324, 'ok!', 'text');

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

        $request = self::getRequest(747719238, 59673324, 'ok!', 'text');

        $tbp = new TelegramBotPlatform(self::getConfig(), $request);

        $this->assertNotNull($tbp->getConfigManager());
        $this->assertEquals(747719238, $tbp->getConfigManager()->getUpdate()->getUpdateId());
    }

    /**
     * @throws TelegramBotAPIException
     */
    public function testGetTelegramBotAPI() {

        $request = self::getRequest(747719238, 59673324, 'ok!', 'text');
        $config = self::getConfig();

        $tbp = new TelegramBotPlatform(self::getConfig(), $request);

        $this->assertNotNull($tbp->getTelegramBotAPI());
        $this->assertEquals($config['token'], $tbp->getTelegramBotAPI()->getToken());
    }

    /**
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testGetUpdateHandler() {

        $request = self::getRequest(747719238, 59673324, 'ok!', 'text');

        $tbp = new TelegramBotPlatform(self::getConfig(), $request);

        $this->assertNotNull($tbp->getUpdateHandler());
    }

    /**
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     * @expectedException \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testSetSessionEmpty() {

        $request = self::getRequest(747719238, 59673324, 'ok!', 'text');

        $tbp = new TelegramBotPlatform(self::getConfig(), $request);

        $tbp->setSession(array());
    }

    /**
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testSetGetSession() {

        $request = self::getRequest(747719238, 59673324, 'ok!', 'text');

        $tbp = new TelegramBotPlatform(self::getConfig(), $request);

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

        $request = self::getRequest(747719238, 59673324, 'ok!', 'text');

        $tbp = new TelegramBotPlatform(self::getConfig(), $request);

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

        $tbp = new TelegramBotPlatform(self::getConfig(), $request);

        $tbp->run();
    }
}
