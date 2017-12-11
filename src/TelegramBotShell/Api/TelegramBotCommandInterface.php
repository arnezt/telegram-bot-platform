<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 07.12.17
 * Time: 18:48
 */

namespace TelegramBotShell\Api;


use TelegramBotAPI\Types\Update;
use TelegramBotShell\TelegramBotShell;

/**
 * This interface must be implemented by all telegram commands
 * in order to have an entry point to command.
 *
 * Interface TelegramBotCommandInterface
 * @package TelegramBotShell\Api
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
interface TelegramBotCommandInterface {

    /**
     * This method is started and is the entry point to the command.
     *
     * @param TelegramBotShell $tbs
     * @param Update $update
     * @param mixed|null $payload
     * @return bool
     */
    public function execute(TelegramBotShell $tbs, Update $update, $payload = null);
}
