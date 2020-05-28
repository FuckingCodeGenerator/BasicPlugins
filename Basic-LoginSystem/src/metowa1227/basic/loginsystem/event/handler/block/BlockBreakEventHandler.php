<?php
namespace metowa1227\basic\loginsystem\event\handler\block;

use metowa1227\basic\loginsystem\queue\Queue;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

class BlockBreakEventHandler implements Listener
{
    public function playerChat(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();

        if (Queue::inQueue($player->getName())) {
            $event->setCancelled();
        }
    }
}
