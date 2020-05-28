<?php
namespace metowa1227\basic\loginsystem\queue;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Queue
{
    /** @var string[] */
    private static $inQueue = [];

    public static function inQueue(string $name): bool
    {
        return isset(self::$inQueue[$name]);
    }

    public static function addQueue(Player $player): void
    {
        if (self::inQueue($player->getName())) {
            return;
        }

        $player->setNameTag(TextFormat::DARK_RED . "[Not logged in] " . TextFormat::RESET . $player->getNameTag());
        $player->setDisplayName(TextFormat::DARK_RED . "[Not logged in] " . TextFormat::RESET . $player->getDisplayName());
        $player->setImmobile(true);
        self::$inQueue[$player->getName()] = true;
    }

    public static function removeQueue(Player $player)
    {
        if (!self::inQueue($player->getName())) {
            return;
        }

        $player->setNameTag(str_replace(TextFormat::DARK_RED . "[Not logged in] " . TextFormat::RESET, "", $player->getNameTag()));
        $player->setDisplayName(str_replace(TextFormat::DARK_RED . "[Not logged in] " . TextFormat::RESET, "", $player->getDisplayName()));
        $player->setImmobile(false);
        unset(self::$inQueue[$player->getName()]);
    }
}
