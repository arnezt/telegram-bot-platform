<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 07.12.17
 * Time: 18:48
 */

namespace TelegramBotShell;


use TelegramBotAPI\TelegramBotAPI;
use TelegramBotShell\Core\ConfigManager;
use TelegramBotShell\Core\UpdateHandler;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotShell\Exception\TelegramBotShellException;

/**
 * @package TelegramBotShell
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class TelegramBotShell {

    const CACHE_PREFIX = 'TELEGRAM_SESSION_';


    /**
     * @var ConfigManager $cm
     */
    private $cm;

    /**
     * @var UpdateHandler $uh
     */
    private $uh;


    /**
     * @return ConfigManager
     */
    public function getConfigManager() {
        return $this->cm;
    }

    /**
     * @param ConfigManager $cm
     */
    public function setConfigManager(ConfigManager $cm) {
        $this->cm = $cm;
    }

    /**
     * @return UpdateHandler
     */
    public function getUpdateHandler() {
        return $this->uh;
    }

    /**
     * @param UpdateHandler $uh
     */
    public function setUpdateHandler(UpdateHandler $uh) {
        $this->uh = $uh;
    }

    /**
     * @api
     * @link https://core.telegram.org/bots/api
     * @return TelegramBotAPI
     */
    public function getTelegramBotAPI() {
        return $this->getConfigManager()->getTelegramBotAPI();
    }


    /**
     * @api
     * @link https://github.com/jungle-bay/telegram-bot-shell/blob/master/docs/api.md#2-setcontext
     * @param array $context
     */
    public function setContext(array $context) {

        $key = self::CACHE_PREFIX . $context['id'];

        $this->getConfigManager()->getCache()->set($key, $context['context'], 86400);
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function getContext($id) {
        return $this->getConfigManager()->getCache()->get(self::CACHE_PREFIX . $id);
    }

    /**
     * @api
     * @link https://github.com/jungle-bay/telegram-bot-shell/blob/master/docs/api.md#3-deletecontext
     * @param string $id
     */
    public function deleteContext($id) {
        $this->getConfigManager()->getCache()->delete(self::CACHE_PREFIX . $id);
    }


    /**
     * @api
     * @link https://github.com/jungle-bay/telegram-bot-shell/blob/master/docs/api.md#4-run
     * @throws TelegramBotAPIException
     * @throws TelegramBotShellException
     */
    public function run() {
        $this->getUpdateHandler()->runParser($this);
    }


    /**
     * @api
     * @param array $config
     * @param string $request
     */
    public function __construct(array $config, $request) {

        $this->setConfigManager(new ConfigManager($config, $request));
        $this->setUpdateHandler(new UpdateHandler($this->getConfigManager()));
    }
}
