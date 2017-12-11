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
use TelegramBotPlatform\TelegramBotShell;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;
use TelegramBotAPI\Exception\TelegramBotAPIException;

/**
 * Class TelegramBotShellTest
 * @package TelegramBotShell\Tests
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class TelegramBotShellTest extends TestCase {

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
        $mock = $mockBuilder->getMock();

        $adapter = new MemoryStore();

        return array(
            'token'    => '479218867:AAGjGTwl0F-prMPIC6-AkNuLD1Bb2tRsYbc',
            'adapter'  => $adapter,
            'payload'  => null,
            'commands' => array(
                'default'  => get_class($mock),
                'mappings' => array(
                    'help' => get_class($mock),
                    'user' => get_class($mock)
                )
            )
        );
    }


    /**
     * @return array
     */
    public function dataRequest() {

        $one = $this->getRequest(747719235, 59673324, '/cmd', 'bot_command');
        $two = $this->getRequest(747719236, 59673324, '/user', 'bot_command');
        $free = $this->getRequest(747719237, 59673324, 'Nike Cortez', 'text');
        $four = $this->getRequest(747719238, 59673324, 'ok!', 'text');

        return array(
            array($one),
            array($two),
            array($free),
            array($four)
        );
    }


    public function testGetConfigManager() {

        $request = $this->getRequest(747719238, 59673324, 'ok!', 'text');

        $tbs = new TelegramBotShell($this->getConfig(), $request);

        $this->assertNotNull($tbs->getConfigManager());
        $this->assertEquals(747719238, $tbs->getConfigManager()->getUpdate()->getUpdateId());
    }

    /**
     * @throws TelegramBotAPIException
     */
    public function testGetTelegramBotAPI() {

        $request = $this->getRequest(747719238, 59673324, 'ok!', 'text');
        $config = $this->getConfig();

        $tbs = new TelegramBotShell($config, $request);

        $this->assertNotNull($tbs->getTelegramBotAPI());
        $this->assertEquals($config['token'], $tbs->getTelegramBotAPI()->getToken());
    }

    public function testGetUpdateHandler() {

        $request = $this->getRequest(747719238, 59673324, 'ok!', 'text');

        $tbs = new TelegramBotShell($this->getConfig(), $request);

        $this->assertNotNull($tbs->getUpdateHandler());
    }


    public function testSetGetContext() {

        $request = $this->getRequest(747719238, 59673324, 'ok!', 'text');

        $tbs = new TelegramBotShell($this->getConfig(), $request);

        $tbs->setContext(array(
            'id'      => $tbs->getConfigManager()->getUpdate()->getMessage()->getChat()->getId(),
            'context' => array(
                'command' => 'Cmd',
                'method'  => 'execute'
            )
        ));

        $context = $tbs->getContext($tbs->getConfigManager()->getUpdate()->getMessage()->getChat()->getId());

        $this->assertNotNull($context);
        $this->assertEquals('Cmd', $context['command']);
        $this->assertEquals('execute', $context['method']);
    }

    public function testDeleteContext() {

        $request = $this->getRequest(747719238, 59673324, 'ok!', 'text');

        $tbs = new TelegramBotShell($this->getConfig(), $request);

        $tbs->deleteContext($tbs->getConfigManager()->getUpdate()->getMessage()->getChat()->getId());

        $context = $tbs->getContext($tbs->getConfigManager()->getUpdate()->getMessage()->getChat()->getId());

        $this->assertNull($context);
    }

    /**
     * @param string $request
     * @throws TelegramBotAPIException
     * @dataProvider dataRequest
     * @expectedException \TelegramBotPlatform\Exception\TelegramBotShellException
     */
    public function testRun($request) {

        $tbs = new TelegramBotShell($this->getConfig(), $request);

        $tbs->run();
    }
}
