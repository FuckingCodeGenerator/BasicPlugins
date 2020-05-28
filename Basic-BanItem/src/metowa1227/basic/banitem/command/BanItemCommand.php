<?php
namespace metowa1227\basic\banitem\command;

use metowa1227\basic\banitem\plugin\BanItem;
use pocketmine\command\Command;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\item\Item;
use pocketmine\Player;

class BanItemCommand extends Command
{
    private const NAME = "banitem";
    private const DESCRIPTION = "BanItem のマスターコマンドです";
    private const USAGE = "/banitem help";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, self::USAGE);

        $this->setPermission("basic.banitem.command.banitem");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        if (count($args) < 1) {
            throw new InvalidCommandSyntaxException();
        }
        if (!$this->testPermission($sender)) {
            return false;
        }

        switch ($args[0]) {
            case "help":
                $sender->sendMessage("/banitem:");
                $sender->sendMessage("help: ヘルプを表示");
                $sender->sendMessage("add: アイテムをBAN");
                $sender->sendMessage("remove: BANを解除");
                $sender->sendMessage("list: BANされたアイテムの一覧");
            break;
            case "add":
                if (count($args) < 2) {
                    $sender->sendMessage("使用法: /banitem add <ItemID>");
                    $sender->sendMessage("<ItemID> を \"hand\" にすると、手に持っているアイテムを適用できます");
                    return;
                }

                $item = null;
                if ($args[1] === "hand") {
                    if (!$sender instanceof Player) {
                        $sender->sendMessage("Handを利用するにはゲーム内で実行してください");
                        return;
                    }
                    $item = $sender->getInventory()->getItemInHand();
                }

                if ($item === null) {
                    $item = Item::fromString($args[1]);
                }

                BanItem::setBanned($item, true);
                $sender->sendMessage("アイテム " . $item->getName() . " をBANしました");
            break;
            case "remove":
                if (count($args) < 2) {
                    $sender->sendMessage("使用法: /banitem remove <ItemID>");
                    $sender->sendMessage("<ItemID> を \"hand\" にすると、手に持っているアイテムを適用できます");
                    return;
                }

                $item = null;
                if ($args[1] === "hand") {
                    if (!$sender instanceof Player) {
                        $sender->sendMessage("Handを利用するにはゲーム内で実行してください");
                        return;
                    }
                    $item = $sender->getInventory()->getItemInHand();
                }

                if ($item === null) {
                    $item = Item::fromString($args[1]);
                }

                BanItem::setBanned($item, false);
                $sender->sendMessage("アイテム " . $item->getName() . " のBANを解除しました");
            break;
            case "list":
                $result = "BANされたアイテム\n";

                if (empty(BanItem::getBannedItems())) {
                    $result .= "なし";
                }

                foreach (BanItem::getBannedItems() as $itemId => $value) {
                    $item = Item::fromString($itemId);
                    $result .= "ID: " . BanItem::convertToString($item) . " Name: " . $item->getName() . "\n";
                }
                $sender->sendMessage($result);
            break;
            default:
                throw new InvalidCommandSyntaxException();
            break;
        }
    }
}
