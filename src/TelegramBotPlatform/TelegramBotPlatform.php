<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 07.12.17
 * Time: 18:48
 */

namespace TelegramBotPlatform;


use TelegramBotAPI\TelegramBotAPI;
use TelegramBotPlatform\Core\ConfigManager;
use TelegramBotPlatform\Core\UpdateHandler;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotPlatform\Exception\TelegramBotPlatformException;

/**
 * Class TelegramBotPlatform
 * @package TelegramBotPlatform
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class TelegramBotPlatform {

    const TELEGRAM_SESSION_PREFIX = 'TELEGRAM_SESSION_';


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
     * @link https://github.com/jungle-bay/telegram-bot-shell/blob/master/docs/api.md#2-setsession
     * @param array $session
     * @return bool
     * @throws TelegramBotPlatformException
     */
    public function setSession(array $session) {

        switch (true) {

            case true === empty($session['id']):
                throw new TelegramBotPlatformException('id is a required field.');

            case true === empty($session['context']):
                throw new TelegramBotPlatformException('context is a required field.');

            case true === empty($session['context']['class']):
                throw new TelegramBotPlatformException('context.class is a required field.');

            case true === empty($session['context']['method']):
                throw new TelegramBotPlatformException('context.method is a required field.');

            default:

                $key = self::TELEGRAM_SESSION_PREFIX . $session['id'];

                return $this->getConfigManager()->getStorage()->set($key, $session['context'], 86400);
        }
    }

    /**
     * @param string|int $id
     * @return array|null
     */
    public function getSession($id) {
        return $this->getConfigManager()->getStorage()->get(self::TELEGRAM_SESSION_PREFIX . $id);
    }

    /**
     * @api
     * @link https://github.com/jungle-bay/telegram-bot-shell/blob/master/docs/api.md#3-deletesession
     * @param string|int $id
     * @return bool
     */
    public function deleteSession($id) {
        return $this->getConfigManager()->getStorage()->delete(self::TELEGRAM_SESSION_PREFIX . $id);
    }


    /**
     * @api
     * @link https://github.com/jungle-bay/telegram-bot-shell/blob/master/docs/api.md#4-run
     * @throws TelegramBotAPIException
     * @throws TelegramBotPlatformException
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
