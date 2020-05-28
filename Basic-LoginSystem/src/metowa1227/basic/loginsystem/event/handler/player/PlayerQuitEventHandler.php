<?php
namespace metowa1227\basic\loginsystem\event\handler\player;

use metowa1227\basic\loginsystem\queue\Queue;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerQuitEventHandler implements Listener
{
    public function playerQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();

        if (Queue::inQueue($player->getName())) {
            Queue::removeQueue($player);
        }
    }
}
