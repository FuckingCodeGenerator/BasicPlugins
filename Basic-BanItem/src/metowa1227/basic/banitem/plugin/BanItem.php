<?php
namespace metowa1227\basic\banitem\plugin;

use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class BanItem extends PluginBase
{
    /** @var string[] */
    private static $bannedItems = [];

    /** @var Config */
    private $bannedItemsConfig;

    /**
     * アイテムがBANされているか
     *
     * @param Item|Block $item
     * @return boolean
     */
    public static function isBannedItem($item): bool
    {
        return isset(self::$bannedItems[self::convertToString($item)]);
    }

    /**
     * アイテムBANを設定
     *
     * @param Item|Block $item
     * @return void
     */
    public static function setBanned($item, bool $value): void
    {
        if ($value) {
            if (self::isBannedItem($item)) {
                return;
            }

            self::$bannedItems[self::convertToString($item)] = true;
        } else {
            if (!self::isBannedItem($item)) {
                return;
            }

            unset(self::$bannedItems[self::convertToString($item)]);
        }
    }

    /**
     * BAnされたアイテムを取得
     *
     * @return array
     */
    public static function getBannedItems(): array
    {
        return self::$bannedItems;
    }

    /**
     * アイテムを文字列に変換
     *
     * @param Item|Block $item
     * @return string
     */
    public static function convertToString($item): string
    {
        return $item->getId() . ":" . $item->getDamage();
    }

    public function onEnable()
    {
        $this->loadBannedItems();
        $this->registerCommands();
        $this->registerEvents();
    }

    public function onDisable()
    {
        $this->saveData();
    }

    /**
     * データを保存
     *
     * @return void
     */
    private function saveData(): void
    {
        $this->bannedItemsConfig->setAll(self::$bannedItems);
        $this->bannedItemsConfig->save();
    }

    /**
     * BANされたアイテムの読み込み
     *
     * @return void
     */
    private function loadBannedItems(): void
    {
        $this->bannedItemsConfig = new Config($this->getDataFolder() . "BannedItems.yml");
        self::$bannedItems = $this->bannedItemsConfig->getAll();
    }

    private $eventList = [
        "metowa1227\basic\banitem\\event\handler\block\BlockPlaceEventHandler",
        "metowa1227\basic\banitem\\event\handler\player\PlayerInteractEventHandler"
    ];
    private function registerEvents(): void
    {
        foreach ($this->eventList as $event) {
            $this->getServer()->getPluginManager()->registerEvents(new $event, $this);
        }
    }

    private $commandList = [
        "metowa1227\basic\banitem\command\BanItemCommand"
    ];
    private function registerCommands(): void
    {
        foreach ($this->commandList as $command) {
            $this->getServer()->getCommandMap()->register($this->getName(), new $command);
        }
    }
}
