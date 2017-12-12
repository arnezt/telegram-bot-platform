<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 12.12.17
 * Time: 14:05
 */

namespace TelegramBotPlatform\Api;


use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotPlatform\Exception\TelegramBotPlatformException;

/**
 * Interface TelegramUpdateHandlerInterface
 *
 * To override the logic of the Update handler, implement
 * this interface and transfer it to TelegramBotPlatform before call method run.
 *
 * @package TelegramBotPlatform\Api
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
interface TelegramUpdateHandlerInterface {

    /**
     * @param TelegramBotPlatform $tbp
     * @throws TelegramBotPlatformException
     */
    public function runParser(TelegramBotPlatform $tbp);
}
