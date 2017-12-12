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


use TelegramBotPlatform\TelegramBotPlatform;
use TelegramBotAPI\Exception\TelegramBotAPIException;
use TelegramBotPlatform\Api\TelegramBotCommandInterface;
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
     * @var ConfigManager $cm
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

                $isWork = $this->executeInlineQuery($tbp);

                if (true === $isWork) return true;

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
     * {@inheritdoc}
     */
    public function runParser(TelegramBotPlatform $tbp) {

        if (true === $this->executeCommand($tbp)) return;

        elseif (true === $this->executeSession($tbp)) return;

        elseif (true === $this->executeDefault($tbp)) return;

        else throw new TelegramBotPlatformException('Something went wrong...');
    }


    /**
     * UpdateHandler constructor.
     * @param ConfigManager $cm
     */
    public function __construct(ConfigManager $cm) {
        $this->setConfigManager($cm);
    }
}
