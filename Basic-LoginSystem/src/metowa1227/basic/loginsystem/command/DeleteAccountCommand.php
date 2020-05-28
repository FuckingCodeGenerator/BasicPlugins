<?php
namespace metowa1227\basic\loginsystem\command;

use metowa1227\basic\loginsystem\account\AccountManager;
use pocketmine\command\Command;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Player;
use pocketmine\Server;

class DeleteAccountCommand extends Command
{
    private const NAME = "deleteac";
    private const DESCRIPTION = "アカウントを削除";
    private const USAGE = "/deleteac <password>";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, self::USAGE);
        
        $this->setPermission("basic.loginsystem.command.deleteac");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->isOp()) {
            if (count($args) < 1) {
                throw new InvalidCommandSyntaxException("/deleteac <username>");
            }

            $target = $args[0];
            if (!AccountManager::existAccount($target)) {
                $sender->sendMessage($target . " のアカウントは存在しません");
                return;
            }

            AccountManager::deleteAccount($target);

            if (($targetPlayer = Server::getInstance()->getPlayer($target)) instanceof Player) {
                $targetPlayer->kick("あなたのアカウントは権限者によって削除されました");
            }

            Server::getInstance()->getLogger()->notice($target . " のアカウントが " . $sender->getName() . " によって削除されました");
            $sender->sendMessage($target . " のアカウントを削除しました");
            return;
        }

        if (count($args) < 1) {
            throw new InvalidCommandSyntaxException();
        }

        $password = $args[0];
        if (!AccountManager::getAccount($sender->getName())->login($password)) {
            $sender->sendMessage("パスワードが認証できませんでした");
            return;
        }

        AccountManager::deleteAccount($sender->getName());
        if ($sender instanceof Player) {
            $sender->kick("アカウントを削除しました");
        }
    }
}
