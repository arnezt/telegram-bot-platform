<?php
/**
 * Created by PhpStorm.
 * Team: jungle
 * User: Roma Baranenko
 * Contacts: <jungle.romabb8@gmail.com>
 * Date: 07.12.17
 * Time: 18:48
 */

namespace TelegramBotShell\Core;


use TelegramBotAPI\Types\Update;
use TelegramBotAPI\TelegramBotAPI;
use MatthiasMullie\Scrapbook\KeyValueStore;
use MatthiasMullie\Scrapbook\Psr16\SimpleCache;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotAPI\Exception\TelegramBotAPIRuntimeException;
use TelegramBotShell\Exception\TelegramBotShellException;

/**
 * Class ConfigManager
 * @package TelegramBotShell\Core
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class ConfigManager {

    /**
     * @var TelegramBotAPI $tba
     */
    private $tba;

    /**
     * @var SimpleCache $cache
     */
    private $cache;

    /**
     * @var string|null $defaultCommand
     */
    private $defaultCommand;

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

        if (true === empty($config['token'])) throw new TelegramBotAPIException('Token empty.');

        $this->tba = new TelegramBotAPI($config['token']);
    }

    /**
     * @param array $config
     * @throws TelegramBotShellException
     */
    private function initCache(array $config) {

        if (true === empty($config['adapter'])) {
            throw new TelegramBotShellException('Adapter is a required field.');
        }

        if (false === ($config['adapter'] instanceof KeyValueStore)) {
            throw new TelegramBotShellException('Adapter must be an implements ' . KeyValueStore::class);
        }

        $this->cache = new SimpleCache($config['adapter']);
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
    private function initCommands(array $config) {

        if (true === empty($config['commands'])) {

            $this->defaultCommand = null;
            $this->commands = array();

            return;
        }

        $this->initDefaultCommand($config['commands']);

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
     * @return TelegramBotAPI
     */
    public function getTelegramBotAPI() {
        return $this->tba;
    }

    /**
     * @return SimpleCache
     */
    public function getCache() {
        return $this->cache;
    }

    /**
     * @return string|null
     */
    public function getDefaultCommand() {
        return $this->defaultCommand;
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
     * @return Update
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
        $this->initCache($config);
        $this->initCommands($config);
        $this->initPayload($config);
        $this->initUpdate($request);
    }
}
