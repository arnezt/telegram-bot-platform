<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 07.12.17
 * Time: 18:48
 */

namespace TelegramBotPlatform\Api;


use TelegramBotAPI\Types\Update;
use TelegramBotPlatform\TelegramBotPlatform;

/**
 * Interface TelegramBotCommandInterface
 *
 * This interface must be implemented by all telegram commands
 * in order to have an entry point to command.
 *
 * @package TelegramBotPlatform\Api
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
interface TelegramBotCommandInterface {

    /**
     * This method is started and is the entry point to the command.
     *
     * @param TelegramBotPlatform $tbp
     * @param Update $update
     * @param mixed|null $payload
     * @return bool Return true if successful.
     */
    public function execute(TelegramBotPlatform $tbp, Update $update, $payload = null);
}
