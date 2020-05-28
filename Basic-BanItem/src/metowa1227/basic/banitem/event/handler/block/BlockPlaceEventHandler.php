<?php
namespace metowa1227\basic\banitem\event\handler\block;

use metowa1227\basic\banitem\plugin\BanItem;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class BlockPlaceEventHandler implements Listener
{
    public function blockPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if (BanItem::isBannedItem($block)) {
            $player->sendMessage("このアイテムの使用は禁止されています");
            $event->setCancelled();
        }
    }
}
