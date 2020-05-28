<?php
namespace metowa1227\basic\loginsystem\command;

use metowa1227\basic\loginsystem\account\AccountManager;
use metowa1227\basic\loginsystem\plugin\LoginSystem;
use metowa1227\basic\loginsystem\queue\Queue;
use pocketmine\command\Command;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Player;

class LoginCommand extends Command
{
    private const NAME = "login";
    private const DESCRIPTION = "アカウントにログイン";
    private const USAGE = "/login <password>";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, self::USAGE);
        
        $this->setPermission("basic.loginsystem.command.login");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("ゲーム内から実行してください");
            return;
        }
        if (count($args) < 1) {
            throw new InvalidCommandSyntaxException();
        }

        $password = $args[0];

        if (!AccountManager::existAccount($sender->getName())) {
            $sender->sendMessage("ログインするには、まず /register コマンドでアカウントを作成してください");
            return;
        }

        $account = AccountManager::getAccount($sender->getName());

        if (!$account->login($password)) {
            $banCountdown = "";
            if (LoginSystem::isEnabledAutoBan()) {
                $count = $account->getNumberOfRemainingMistake();
                $banCountdown = " 残り回数: " . $count;
                $account->decrementNumberOfRemainingMistake();

                if ($count === 0) {
                    $sender->kick("パスワード認証の回数制限に達しました\nあなたのアカウントはサーバーから追放されました");
                    $sender->setBanned(true);
                    $account->deleteAccount();
                }
            }

            $sender->sendMessage("パスワードが認証できませんでした" . $banCountdown);
            return;
        }

        $account->resetNumberOfRemainingMistake();
        $account->resetPasswordRequiredCount();
        Queue::removeQueue($sender);
        $sender->sendMessage("ログインに成功しました");
    }
}
