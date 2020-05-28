<?php
namespace metowa1227\basic\loginsystem\model;

use metowa1227\basic\loginsystem\plugin\LoginSystem;
use pocketmine\Player;

class AccountModel
{
    /** @var string */
    protected $userName, $password, $ipAddress, $clientId;

    /**
     * パスワードを間違えることができる残り回数
     *
     * @var int
     */
    protected $numberOfRemainingMistake;

    /**
     * パスワード入力が必要になるまでのログイン回数
     *
     * @var int
     */
    protected $passwordRequired;

    public function __construct(Player $player, string $password)
    {
        $this->userName = $player->getName();
        $this->ipAddress = $player->getAddress();
        $this->clientId = $player->getClientId();
        $this->password = password_hash($password, PASSWORD_DEFAULT); // undefined PASSWORD_ARGON2ID

        $this->numberOfRemainingMistake = LoginSystem::getConfigArray()["wrong-password-count-to-ban"];
        $this->passwordRequired = 20;
    }
}
