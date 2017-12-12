<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 12.12.17
 * Time: 14:29
 */

namespace TelegramBotPlatform\Api;


use TelegramBotAPI\Types\Update;
use TelegramBotAPI\TelegramBotAPI;
use MatthiasMullie\Scrapbook\Psr16\SimpleCache;

/**
 * Interface TelegramConfigManagerInterface
 *
 * To redefine the configuration handler logic, implement
 * this interface and transfer it to TelegramBotPlatform before call method run.
 *
 * @package TelegramBotPlatform\Api
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
interface TelegramConfigManagerInterface {

    /**
     * @return TelegramBotAPI
     */
    public function getTelegramBotAPI();

    /**
     * @return SimpleCache
     */
    public function getStorage();

    /**
     * @return Update
     */
    public function getUpdate();
}
