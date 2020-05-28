<?php
namespace metowa1227\basic\loginsystem\event\handler\player;

use metowa1227\basic\loginsystem\account\Account;
use metowa1227\basic\loginsystem\account\AccountManager;
use metowa1227\basic\loginsystem\plugin\LoginSystem;
use metowa1227\basic\loginsystem\queue\Queue;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;

class PlayerJoinEventHandler implements Listener
{
    public function playerJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $playerName = $player->getName();

        if (!AccountManager::existAccount($playerName)) {
            $player->sendMessage("ようこそ " . $playerName . " さん");
            $player->sendMessage("このサーバーではログインシステムを採用しており、サーバーで遊ぶためにはアカウントを作成する必要があります");
            $player->sendMessage("/register コマンドを使用してアカウントを作成してください");
            Queue::addQueue($player);
            return;
        }

        $account = AccountManager::getAccount($playerName);

        if (LoginSystem::getConfigArray()["auto-login"]) {
            if ($this->needLogin($account, $player)) {
                $player->sendMessage("ログインが必要です");
                $player->sendMessage("/login コマンドでログインしてください");
                Queue::addQueue($player);
            } else {
                $account->decrementPasswordRequiredCount();
                $player->sendMessage("自動ログインに成功しました");
            }
        }
    }

    private function needLogin(Account $account, Player $player): bool
    {
        if ($account->getIpAddress() !== $player->getAddress()) {
            return true;
        }
        if ($account->getClientId() != $player->getClientId()) {
            return true;
        }
        if ($account->getPasswordRequired() === 0) {
            return true;
        }

        return false;
    }
}
