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
     * @return array
     */
    public function dataProvider() {

        $oneRequest = TelegramBotPlatformTest::getRequest(747719235, 59673324, '/cmd', 'bot_command');
        $twoRequest = TelegramBotPlatformTest::getRequest(747719235, 59673324, '/test', 'bot_command');

        return array(
            array($oneRequest),
            array($twoRequest)
        );
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
     * @param $request
     * @dataProvider dataProvider
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function testRunParser($request) {

        $tbp = new TelegramBotPlatform(TelegramBotPlatformTest::getConfig(), $request);

        $tbp->getUpdateHandler()->runParser($tbp);
    }
}
