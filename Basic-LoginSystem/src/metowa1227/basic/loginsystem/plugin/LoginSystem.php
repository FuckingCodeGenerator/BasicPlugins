<?php
namespace metowa1227\basic\loginsystem\plugin;

use metowa1227\basic\loginsystem\account\AccountManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class LoginSystem extends PluginBase
{
    /** @var Config */
    private static $config;

    /**
     * パスワードミスによる自動BANが有効か
     *
     * @return boolean
     */
    public static function isEnabledAutoBan(): bool
    {
        return self::getConfigArray()["wrong-password-count-to-ban"] !== -1;
    }

    /**
     * パスワードの最低文字数を取得
     *
     * @return integer
     */
    public static function getMinimumPasswordLength(): int
    {
        return self::getConfigArray()["minimum-password-length"];
    }

    /**
     * パスワードの最大文字数を取得
     *
     * @return integer
     */
    public static function getMaximumPasswordLength(): int
    {
        return self::getConfigArray()["maximum-password-length"];
    }

    /**
     * コンフィグのデータを配列形式で取得
     *
     * @return array
     */
    public static function getConfigArray(): array
    {
        return self::$config->getAll();
    }

    public function onEnable()
    {
        self::$config = $this->getConfig();

        AccountManager::init($this, $this->getDataFolder() . "Account");

        $this->reigsterCommands();
        $this->registerEvents();

        $this->getLogger()->info(count(AccountManager::getAccounts()) . " accounts available.");
    }

    public function onDisable()
    {
        AccountManager::saveData();
    }

    private $commandList = [
        "metowa1227\basic\loginsystem\command\RegisterCommand",
        "metowa1227\basic\loginsystem\command\LoginCommand",
        "metowa1227\basic\loginsystem\command\ChangePasswordCommand",
        "metowa1227\basic\loginsystem\command\DeleteAccountCommand"
    ];
    private function reigsterCommands(): void
    {
        foreach ($this->commandList as $command) {
            $this->getServer()->getCommandMap()->register($this->getName(), new $command);
        }
    }

    private $eventList = [
        "metowa1227\basic\loginsystem\\event\handler\player\PlayerJoinEventHandler",
        "metowa1227\basic\loginsystem\\event\handler\player\PlayerQuitEventHandler",
        "metowa1227\basic\loginsystem\\event\handler\player\PlayerInteractEventHandler",
        "metowa1227\basic\loginsystem\\event\handler\player\PlayerCommandPreProcessEventHandler",
        "metowa1227\basic\loginsystem\\event\handler\block\BlockPlaceEventHandler",
        "metowa1227\basic\loginsystem\\event\handler\block\BlockBreakEventHandler",
    ];
    private function registerEvents(): void
    {
        foreach ($this->eventList as $event) {
            $this->getServer()->getPluginManager()->registerEvents(new $event, $this);
        }
    }
}
