<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 13.12.17
 * Time: 22:41
 */

namespace TelegramBotPlatform\Tests\Stubs;


use TelegramBotAPI\Types\Update;
use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;

/**
 * Class TestCmdTestStub
 * @package TelegramBotPlatform\Tests\Stubs
 */
class TestCmdTestStub implements TelegramBotCommandInterface {

    /**
     * @param TelegramBotPlatform $tbp
     * @param Update $update
     * @param mixed|null $payload
     * @return bool Return true if successful.
     * @throws \TelegramBotPlatform\Exception\TelegramBotPlatformException
     */
    public function next(TelegramBotPlatform $tbp, Update $update, $payload = null) {

        $tbp->deleteSession($update->getMessage()->getChat()->getId());

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function execute(TelegramBotPlatform $tbp, Update $update, $payload = null) {

        $tbp->setSession(array(
            'id'      => $update->getMessage()->getChat()->getId(),
            'context' => array(
                'class'  => self::class,
                'method' => 'next'
            )
        ));

        return true;
    }
}
