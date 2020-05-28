<?php
namespace metowa1227\basic\loginsystem\command;

use metowa1227\basic\loginsystem\account\Account;
use metowa1227\basic\loginsystem\account\AccountManager;
use metowa1227\basic\loginsystem\plugin\LoginSystem;
use metowa1227\basic\loginsystem\queue\Queue;
use pocketmine\command\Command;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Player;

class RegisterCommand extends Command
{
    private const NAME = "register";
    private const DESCRIPTION = "アカウントを作成";
    private const USAGE = "/register <password> <confirm password>";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, self::USAGE);
        
        $this->setPermission("basic.loginsystem.command.register");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("ゲーム内から実行してください");
            return;
        }
        if (count($args) < 2) {
            throw new InvalidCommandSyntaxException();
        }

        if (AccountManager::existAccount($sender->getName())) {
            $sender->sendMessage("アカウントが既に存在します");
            $sender->sendMessage("パスワードを変更する場合は /cpass <現在のパスワード> <新しいパスワード> <新しいパスワードの確認> を実行してください");
            return;
        }

        $password = $args[0];
        $confirmPassword = $args[1];
        $minimumLength = LoginSystem::getMinimumPasswordLength();
        $maximumLength = LoginSystem::getMaximumPasswordLength();

        if (strlen($password) < $minimumLength) {
            $sender->sendMessage("パスワードは" . $minimumLength . "文字以上に設定してください");
            return;
        }
        if (strlen($password) > $maximumLength) {
            $sender->sendMessage("パスワードは" . $maximumLength . "文字以内に設定してください");
            return;
        }
        if (strcmp($password, $confirmPassword) !== 0) {
            $sender->sendMessage("パスワードが一致しません");
            return;
        }

        Queue::removeQueue($sender);
        new Account($sender, $password);
        $sender->sendMessage("アカウントを作成しました");
        $sender->sendMessage("パスワードは " . $password . " です");
        $sender->sendMessage("パスワードを忘れるとアカウントにログインできません");
    }
}
