<?php
namespace metowa1227\basic\loginsystem\event\handler\player;

use metowa1227\basic\loginsystem\account\AccountManager;
use metowa1227\basic\loginsystem\queue\Queue;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class PlayerCommandPreProcessEventHandler implements Listener
{
    public function playerCommandPreProcess(PlayerCommandPreprocessEvent $event)
    {
        $player = $event->getPlayer();
        $playerName = $player->getName();

        if (Queue::inQueue($playerName)) {
            $command = explode(" ", $event->getMessage())[0];
            if ($command !== "/register" && $command !== "/login") {
                if (AccountManager::existAccount($playerName)) {
                    $player->sendMessage("チャットやコマンドを利用するにはアカウントにログインする必要があります");
                    $player->sendMessage("/login コマンドを使用してログインしてください");    
                } else {
                    $player->sendMessage("チャットやコマンドを利用するにはアカウントを登録する必要があります");
                    $player->sendMessage("/register コマンドを使用してアカウントを作成してください");    
                }
                $event->setCancelled();
            }
        }
    }
}
