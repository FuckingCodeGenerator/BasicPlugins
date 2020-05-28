<?php
namespace metowa1227\basic\loginsystem\event\handler\player;

use metowa1227\basic\loginsystem\queue\Queue;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class PlayerInteractEventHandler implements Listener
{
    public function playerInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();

        if (Queue::inQueue($player->getName())) {
            $event->setCancelled();
        }
    }
}
