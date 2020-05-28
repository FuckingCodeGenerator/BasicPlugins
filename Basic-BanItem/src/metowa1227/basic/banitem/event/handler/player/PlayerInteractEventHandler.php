<?php
namespace metowa1227\basic\banitem\event\handler\player;

use metowa1227\basic\banitem\plugin\BanItem;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class PlayerInteractEventHandler implements Listener
{
    public function PlayerInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if (BanItem::isBannedItem($item)) {
            $player->sendMessage("このアイテムの使用は禁止されています");
            $event->setCancelled();
        }
    }
}
