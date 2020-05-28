<?php
namespace metowa1227\basic\loginsystem\command;

use metowa1227\basic\loginsystem\account\AccountManager;
use metowa1227\basic\loginsystem\plugin\LoginSystem;
use pocketmine\command\Command;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Player;

class ChangePasswordCommand extends Command
{
    private const NAME = "cpass";
    private const DESCRIPTION = "パスワードの変更";
    private const USAGE = "/cpass <現在のパスワード> <新しいパスワード> <新しいパスワードの確認>";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, self::USAGE);
        
        $this->setPermission("basic.loginsystem.command.cpass");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("ゲーム内から実行してください");
            return;
        }
        if (count($args) < 3) {
            throw new InvalidCommandSyntaxException();
        }

        $password = $args[0];
        $newPassword = $args[1];
        $confirmPassword = $args[2];
        $minimumLength = LoginSystem::getMinimumPasswordLength();
        $maximumLength = LoginSystem::getMaximumPasswordLength();
        $account = AccountManager::getAccount($sender->getName());

        if (!$account->login($password)) {
            $sender->sendMessage("現在のパスワードが一致しません");
            return;
        }
        if (strlen($newPassword) < $minimumLength) {
            $sender->sendMessage("パスワードは" . $minimumLength . "文字以上に設定してください");
            return;
        }
        if (strlen($confirmPassword) > $maximumLength) {
            $sender->sendMessage("パスワードは" . $maximumLength . "文字以内に設定してください");
            return;
        }
        if (strcmp($newPassword, $confirmPassword) !== 0) {
            $sender->sendMessage("確認用のパスワードが一致しません");
            return;
        }

        $account->updatePassword($newPassword);
        $sender->sendMessage("パスワードを更新しました: " . $newPassword);
    }
}
