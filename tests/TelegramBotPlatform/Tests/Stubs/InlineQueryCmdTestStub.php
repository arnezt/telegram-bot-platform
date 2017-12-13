<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 13.12.17
 * Time: 22:39
 */

namespace TelegramBotPlatform\Tests\Stubs;


use TelegramBotAPI\Types\Update;
use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;

/**
 * Class InlineQueryCmdTestStub
 * @package TelegramBotPlatform\Tests\Stubs
 */
class InlineQueryCmdTestStub implements TelegramBotCommandInterface {

    /**
     * This method is started and is the entry point to the command.
     *
     * @param TelegramBotPlatform $tbp
     * @param Update $update
     * @param mixed|null $payload
     * @return bool Return true if successful.
     */
    public function execute(TelegramBotPlatform $tbp, Update $update, $payload = null) {
        return true;
    }
}
