<?php
namespace metowa1227\basic\loginsystem\event\handler\block;

use metowa1227\basic\loginsystem\queue\Queue;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class BlockPlaceEventHandler implements Listener
{
    public function playerChat(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();

        if (Queue::inQueue($player->getName())) {
            $event->setCancelled();
        }
    }
}
