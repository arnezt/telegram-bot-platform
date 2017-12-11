# Changelog for Telegram Bot Shell

#### 3.0 / 2017-12-09

* Add `TelegramBotShellException` exception.
* Add `TelegramBotShellContextNotFoundException` exception.
* Add to `TelegramBotCommandInterface` return parameter `bool` if `true` and successfully working command or to default command or try `TelegramBotShellException`.
* Update `chat_id` to `id` from context.
* Update `cmd` to `command` from context.

#### 2.1 / 2017-12-08

* The protection against launching only ```TelegramBotCommandInterface``` commands from the context was removed.

#### 2.0 / 2017-12-08

* The parsing engine object [Update](https://core.telegram.org/bots/api#update).
* The name of the method in the interface has changed.

#### 1.1 / 2017-11-08

* Add full tests.

#### 1.0 / 2017-11-07

* Initial Release.
