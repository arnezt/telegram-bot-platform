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


use TelegramBotShell\TelegramBotShell;
use TelegramBotShell\Api\TelegramBotCommandInterface;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotShell\Exception\TelegramBotShellException;
use TelegramBotShell\Exception\TelegramBotShellContextNotFoundException;

/**
 * Class UpdateHandler
 * @package TelegramBotShell\Core
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class UpdateHandler {

    const BOT_COMMAND = 'bot_command';


    /**
     * @var ConfigManager $cm
     */
    private $cm;


    /**
     * @param mixed $command
     * @throws TelegramBotShellException
     */
    private function firewallCommand($command) {

        if ($command instanceof TelegramBotCommandInterface) return;

        throw new TelegramBotShellException('This ' . get_class($command) . ' not a command of Telegram Bot.');
    }


    /**
     * @param TelegramBotShell $tbs
     * @return bool
     * @throws TelegramBotShellException
     */
    private function executeCommandDefault(TelegramBotShell $tbs) {

        $defaultCommand = $this->getConfigManager()->getDefaultCommand();

        if (null === $defaultCommand) return false;

        $command = new $defaultCommand();

        $this->firewallCommand($command);

        /** @var TelegramBotCommandInterface $command */
        return $command->execute($tbs, $this->getConfigManager()->getUpdate(), $this->getConfigManager()->getPayload());
    }

    /**
     * @param TelegramBotShell $tbs
     * @return bool
     * @throws TelegramBotShellContextNotFoundException
     */
    private function executeContext(TelegramBotShell $tbs) {

        $update = $this->getConfigManager()->getUpdate();

        switch (true) {

            case null !== $update->getMessage():

                $id = $update->getMessage()->getChat()->getId();

                break;

            case null !== $update->getEditedMessage():

                $id = $update->getEditedMessage()->getChat()->getId();

                break;

            case null !== $update->getChannelPost():

                $id = $update->getChannelPost()->getChat()->getId();

                break;

            case null !== $update->getEditedChannelPost():

                $id = $update->getEditedChannelPost()->getChat()->getId();

                break;

            case null !== $update->getInlineQuery():

                $id = $update->getInlineQuery()->getFrom()->getId();

                break;

            case null !== $update->getChosenInlineResult():

                $id = $update->getChosenInlineResult()->getFrom()->getId();

                break;

            case null !== $update->getCallbackQuery():

                $id = $update->getCallbackQuery()->getFrom()->getId();

                break;

            case null !== $update->getShippingQuery():

                $id = $update->getShippingQuery()->getFrom()->getId();

                break;

            case null !== $update->getPreCheckoutQuery():

                $id = $update->getPreCheckoutQuery()->getFrom()->getId();

                break;

            default:
                return false;
        }

        $context = $tbs->getContext($id);

        if (null === $context) return false;

        $classCommand = $context['command'];

        if (false === class_exists($classCommand)) {
            throw new TelegramBotShellContextNotFoundException('There is no such class: ' . $classCommand);
        }

        $method = $context['method'];

        if (false === method_exists($classCommand, $method)) {
            throw new TelegramBotShellContextNotFoundException('There is no method: ' . $method . ' to ' . $classCommand);
        }

        $command = new $classCommand();

        return $command->{$method}($tbs, $update, $this->getConfigManager()->getPayload());
    }

    /**
     * @param TelegramBotShell $tbs
     * @return bool
     * @throws TelegramBotAPIException
     */
    private function executeCommand(TelegramBotShell $tbs) {

        $message = $this->getConfigManager()->getUpdate()->getMessage();

        if (null === $message) return false;

        if (null === $message->getEntities()) return false;

        foreach ($message->getEntities() as $entity) {

            if (self::BOT_COMMAND !== $entity->getType()) continue;

            $cmdWithoutSlash = substr($message->getText(), 1);
            $cmd = explode(' ', $cmdWithoutSlash);
            $cmd = $cmd[0];

            $commands = $this->getConfigManager()->getCommands();

            if (false === array_key_exists($cmd, $commands)) return false;

            $classCommand = $commands[$cmd];

            $command = new $classCommand();

            $this->firewallCommand($command);

            /** @var TelegramBotCommandInterface $command */
            return $command->execute($tbs, $this->getConfigManager()->getUpdate(), $this->getConfigManager()->getPayload());
        }

        return false;
    }


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
     * @param TelegramBotShell $tbs
     * @throws TelegramBotAPIException
     * @throws TelegramBotShellException
     */
    public function runParser(TelegramBotShell $tbs) {

        if (true === $this->executeCommand($tbs)) return;

        elseif (true === $this->executeContext($tbs)) return;

        elseif (true === $this->executeCommandDefault($tbs)) return;

        else throw new TelegramBotShellException('Something went wrong...');
    }


    /**
     * @param ConfigManager $cm
     */
    public function __construct(ConfigManager $cm) {
        $this->setConfigManager($cm);
    }
}
