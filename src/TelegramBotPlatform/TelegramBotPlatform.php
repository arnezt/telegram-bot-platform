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
use TelegramBotPlatform\Api\TelegramConfigManagerInterface;
use TelegramBotPlatform\Api\TelegramUpdateHandlerInterface;
use TelegramBotPlatform\Exception\TelegramBotPlatformException;

/**
 * Class TelegramBotPlatform
 * @package TelegramBotPlatform
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class TelegramBotPlatform {

    const TELEGRAM_SESSION_PREFIX = 'TELEGRAM_SESSION_';


    /**
     * @var TelegramConfigManagerInterface $cm
     */
    private $cm;

    /**
     * @var TelegramUpdateHandlerInterface $uh
     */
    private $uh;


    /**
     * @throws TelegramBotPlatformException
     */
    private function initUpdateHandler() {
        $this->setUpdateHandler(new UpdateHandler($this->getConfigManager()));
    }

    /**
     * @param array $config
     * @param string $request
     */
    private function initConfigManager(array $config, $request) {
        $this->setConfigManager(new ConfigManager($config, $request));
    }


    /**
     * @return TelegramConfigManagerInterface
     * @throws TelegramBotPlatformException
     */
    public function getConfigManager() {

        if (null === $this->cm) throw new TelegramBotPlatformException('Config Manager not initialized.');

        return $this->cm;
    }

    /**
     * @param TelegramConfigManagerInterface $cm
     */
    public function setConfigManager(TelegramConfigManagerInterface $cm) {
        $this->cm = $cm;
    }

    /**
     * @return TelegramUpdateHandlerInterface
     * @throws TelegramBotPlatformException
     */
    public function getUpdateHandler() {

        if (null === $this->uh) $this->initUpdateHandler();

        return $this->uh;
    }

    /**
     * @param TelegramUpdateHandlerInterface $uh
     */
    public function setUpdateHandler(TelegramUpdateHandlerInterface $uh) {
        $this->uh = $uh;
    }

    /**
     * @api
     * @link https://core.telegram.org/bots/api
     * @return TelegramBotAPI
     * @throws TelegramBotPlatformException
     */
    public function getTelegramBotAPI() {
        return $this->getConfigManager()->getTelegramBotAPI();
    }

    /**
     * @api
     * @link https://github.com/jungle-bay/telegram-bot-shell/blob/master/docs/api.md#3-deletesession
     * @param string|int $id
     * @return bool
     * @throws TelegramBotPlatformException
     */
    public function deleteSession($id) {
        return $this->getConfigManager()->getStorage()->delete(self::TELEGRAM_SESSION_PREFIX . $id);
    }

    /**
     * @param string|int $id
     * @return array|null
     * @throws TelegramBotPlatformException
     */
    public function getSession($id) {
        return $this->getConfigManager()->getStorage()->get(self::TELEGRAM_SESSION_PREFIX . $id);
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
    public function __construct(array $config = array(), $request = '') {

        if ((array() === $config) || ('' === $request)) return;

        $this->initConfigManager($config, $request);
    }
}
