<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 07.12.17
 * Time: 18:48
 */

namespace TelegramBotPlatform\Core;


use TelegramBotAPI\Types\Update;
use TelegramBotAPI\TelegramBotAPI;
use MatthiasMullie\Scrapbook\KeyValueStore;
use MatthiasMullie\Scrapbook\Psr16\SimpleCache;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotPlatform\Api\TelegramConfigManagerInterface;
use TelegramBotAPI\Exception\TelegramBotAPIRuntimeException;
use TelegramBotPlatform\Exception\TelegramBotPlatformException;

/**
 * Class ConfigManager
 * @package TelegramBotPlatform\Core
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class ConfigManager implements TelegramConfigManagerInterface {

    /**
     * @var TelegramBotAPI $tba
     */
    private $tba;

    /**
     * @var SimpleCache $storage
     */
    private $storage;

    /**
     * @var null|string $defaultCommand
     */
    private $defaultCommand;

    /**
     * @var null|string $inlineQueryCommand
     */
    private $inlineQueryCommand;

    /**
     * @var array $commands
     */
    private $commands;

    /**
     * @var mixed $payload
     */
    private $payload;

    /**
     * @var Update $update
     */
    private $update;


    /**
     * @param array $config
     * @throws TelegramBotAPIException
     */
    private function initTelegramBotAPI(array $config) {

        if (true === empty($config['token'])) {
            throw new TelegramBotAPIException('Token is a required field.');
        }

        $this->tba = new TelegramBotAPI($config['token']);
    }

    /**
     * @param array $config
     * @throws TelegramBotPlatformException
     */
    private function initStorage(array $config) {

        if (true === empty($config['storage'])) {
            throw new TelegramBotPlatformException('Storage is a required field.');
        }

        if (false === ($config['storage'] instanceof KeyValueStore)) {
            throw new TelegramBotPlatformException('Storage must be an implements ' . KeyValueStore::class);
        }

        $this->storage = new SimpleCache($config['storage']);
    }

    /**
     * @param array $config
     */
    private function initDefaultCommand(array $config) {
        $this->defaultCommand = (true === empty($config['default'])) ? null : $config['default'];
    }

    /**
     * @param array $config
     */
    private function initInlineQueryCommand(array $config) {
        $this->inlineQueryCommand = (true === empty($config['inline_query'])) ? null : $config['inline_query'];
    }

    /**
     * @param array $config
     */
    private function initCommands(array $config) {

        if (true === empty($config['commands'])) {

            $this->defaultCommand = null;
            $this->inlineQueryCommand = null;
            $this->commands = array();

            return;
        }

        $this->initDefaultCommand($config['commands']);
        $this->initInlineQueryCommand($config['commands']);

        $this->commands = (true === empty($config['commands']['mappings'])) ? array() : $config['commands']['mappings'];
    }

    /**
     * @param array $config
     */
    private function initPayload(array $config) {
        $this->payload = (true === empty($config['payload'])) ? null : $config['payload'];
    }

    /**
     * @param string $request
     * @throws TelegramBotAPIException
     * @throws TelegramBotAPIRuntimeException
     */
    private function initUpdate($request) {
        $this->update = $this->getTelegramBotAPI()->setUpdate($request);
    }


    /**
     * {@inheritdoc}
     */
    public function getTelegramBotAPI() {
        return $this->tba;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorage() {
        return $this->storage;
    }

    /**
     * @return null|string
     */
    public function getDefaultCommand() {
        return $this->defaultCommand;
    }

    /**
     * @return null|string
     */
    public function getInlineQueryCommand() {
        return $this->inlineQueryCommand;
    }

    /**
     * @return array
     */
    public function getCommands() {
        return $this->commands;
    }

    /**
     * @return mixed
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdate() {
        return $this->update;
    }


    /**
     * ConfigManager constructor.
     * @param array $config
     * @param string $request
     * @throws TelegramBotAPIException
     */
    public function __construct(array $config, $request) {

        $this->initTelegramBotAPI($config);
        $this->initStorage($config);
        $this->initCommands($config);
        $this->initPayload($config);
        $this->initUpdate($request);
    }
}
