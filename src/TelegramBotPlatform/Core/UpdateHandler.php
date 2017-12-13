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
use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;
use TelegramBotPlatform\Api\TelegramConfigManagerInterface;
use TelegramBotPlatform\Api\TelegramUpdateHandlerInterface;
use TelegramBotPlatform\Exception\TelegramBotPlatformException;
use TelegramBotPlatform\Exception\TelegramBotPlatformNotFoundException;

/**
 * Class UpdateHandler
 * @package TelegramBotPlatform\Core
 * @author Roma Baranenko <jungle.romabb8@gmail.com>
 */
class UpdateHandler implements TelegramUpdateHandlerInterface {

    const BOT_COMMAND = 'bot_command';


    /**
     * @var TelegramConfigManagerInterface $cm
     */
    private $cm;


    /**
     * @param mixed $command
     * @throws TelegramBotPlatformException
     */
    private function firewallCommand($command) {

        if ($command instanceof TelegramBotCommandInterface) return;

        throw new TelegramBotPlatformException('This ' . get_class($command) . ' not a command of Telegram Bot.');
    }


    /**
     * @param Update $update
     * @return int
     * @throws TelegramBotPlatformNotFoundException
     */
    private function getIdByUpdate(Update $update) {

        switch (true) {

            case null !== $update->getMessage():

                return $update->getMessage()->getChat()->getId();

            case null !== $update->getEditedMessage():

                return $update->getEditedMessage()->getChat()->getId();

            case null !== $update->getChannelPost():

                return $update->getChannelPost()->getChat()->getId();

            case null !== $update->getEditedChannelPost():

                return $update->getEditedChannelPost()->getChat()->getId();

            case null !== $update->getInlineQuery():

                return $update->getInlineQuery()->getFrom()->getId();

            case null !== $update->getChosenInlineResult():

                return $update->getChosenInlineResult()->getFrom()->getId();

            case null !== $update->getCallbackQuery():

                return $update->getCallbackQuery()->getFrom()->getId();

            case null !== $update->getShippingQuery():

                return $update->getShippingQuery()->getFrom()->getId();

            case null !== $update->getPreCheckoutQuery():

                return $update->getPreCheckoutQuery()->getFrom()->getId();

            default:
                throw new TelegramBotPlatformNotFoundException('I could not understand the update object.');
        }
    }


    /**
     * @param TelegramBotPlatform $tbp
     * @param mixed $command
     * @param string $method
     * @return bool
     * @throws TelegramBotPlatformException
     */
    private function execute(TelegramBotPlatform $tbp, $command, $method) {

        $command = new $command();

        $this->firewallCommand($command);

        /** @var TelegramBotCommandInterface $command */
        return $command->{$method}($tbp, $this->getConfigManager()->getUpdate(), $this->getConfigManager()->getPayload());
    }


    /**
     * @param TelegramBotPlatform $tbp
     * @return bool
     * @throws TelegramBotPlatformException
     */
    private function executeDefault(TelegramBotPlatform $tbp) {

        $defaultCommand = $this->getConfigManager()->getDefaultCommand();

        if (null === $defaultCommand) return false;

        return $this->execute($tbp, $defaultCommand, 'execute');
    }

    /**
     * @param TelegramBotPlatform $tbp
     * @return bool
     * @throws TelegramBotPlatformException
     */
    private function executeInlineQuery(TelegramBotPlatform $tbp) {

        $update = $this->getConfigManager()->getUpdate();

        if (null === $update->getInlineQuery()) return false;

        $inlineQueryCommand = $this->getConfigManager()->getInlineQueryCommand();

        if (null === $inlineQueryCommand) return false;

        return $this->execute($tbp, $inlineQueryCommand, 'execute');
    }

    /**
     * @param TelegramBotPlatform $tbp
     * @return bool
     * @throws TelegramBotPlatformNotFoundException
     * @throws TelegramBotPlatformException
     */
    private function executeSession(TelegramBotPlatform $tbp) {

        $update = $this->getConfigManager()->getUpdate();

        $id = $this->getIdByUpdate($update);

        $session = $tbp->getSession($id);

        if (null === $session) return false;

        $class = $session['class'];

        if (false === class_exists($class)) {
            throw new TelegramBotPlatformNotFoundException('There is no such class: ' . $class);
        }

        $method = $session['method'];

        if (false === method_exists($class, $method)) {
            throw new TelegramBotPlatformNotFoundException('There is no method: ' . $method . ' to ' . $class);
        }

        $class = new $class();

        return $class->{$method}($tbp, $update, $this->getConfigManager()->getPayload());
    }

    /**
     * @param TelegramBotPlatform $tbp
     * @return bool
     * @throws TelegramBotAPIException
     */
    private function executeCommand(TelegramBotPlatform $tbp) {

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

            $commandClass = $commands[$cmd];

            return $this->execute($tbp, $commandClass, 'execute');
        }

        return false;
    }


    /**
     * @return TelegramConfigManagerInterface
     */
    public function getConfigManager() {
        return $this->cm;
    }

    /**
     * @param TelegramConfigManagerInterface $cm
     */
    public function setConfigManager(TelegramConfigManagerInterface $cm) {
        $this->cm = $cm;
    }


    /**
     * {@inheritdoc}
     */
    public function runParser(TelegramBotPlatform $tbp) {

        if (true === $this->executeCommand($tbp)) return;

        elseif (true === $this->executeInlineQuery($tbp)) return;

        elseif (true === $this->executeSession($tbp)) return;

        elseif (true === $this->executeDefault($tbp)) return;

        else throw new TelegramBotPlatformException('Something went wrong...');
    }


    /**
     * UpdateHandler constructor.
     * @param TelegramConfigManagerInterface $cm
     */
    public function __construct(TelegramConfigManagerInterface $cm) {
        $this->setConfigManager($cm);
    }
}
