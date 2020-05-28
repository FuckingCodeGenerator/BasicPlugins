<?php
namespace metowa1227\basic\loginsystem\account;

use metowa1227\basic\loginsystem\model\AccountModel;
use metowa1227\basic\loginsystem\plugin\LoginSystem;
use pocketmine\Player;

class Account extends AccountModel
{
    /** @var int */
    public const PASSWORD = "password";
    public const BAN_COUNT = "ban_count";
    public const PASSWORD_REQUIRED = "password_required";

    public function __construct(Player $player, string $password)
    {
        parent::__construct($player, $password);

        AccountManager::registerAccount($this);
    }

    /**
     * アカウント名を取得
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->userName;
    }

    /**
     * IPアドレスを取得
     *
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * ClientIdを取得
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * パスワードの再入力までの回数を取得
     *
     * @return integer
     */
    public function getPasswordRequired(): int
    {
        return $this->passwordRequired;
    }

    /**
     * パスワードを取得
     *
     * @return string   hashed password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * BANまでの回数を取得
     *
     * @return integer
     */
    public function getNumberOfRemainingMistake(): int
    {
        return $this->numberOfRemainingMistake;
    }

    /**
     * アカウントデータを削除
     *
     * @return void
     */
    public function deleteAccount(): void
    {
        AccountManager::deleteAccount($this->userName);
    }

    /**
     * パスワードが一致するか
     *
     * @param string $password
     * @return boolean
     */
    public function login(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * パスワードを更新
     *
     * @param string $newPassword
     * @return void
     */
    public function updatePassword(string $newPassword): void
    {
        $this->password = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    /**
     * ログインが必要になるまでの回数を１減らす
     *
     * @return void
     */
    public function decrementPasswordRequiredCount(): void
    {
        $this->passwordRequired--;
    }

    /**
     * ログインが必要になるまでの回数をリセット
     *
     * @return void
     */
    public function resetPasswordRequiredCount(): void
    {
        $this->passwordRequired = 20;
    }

    /**
     * BANになるまでの回数を１減らす
     *
     * @return void
     */
    public function decrementNumberOfRemainingMistake(): void
    {
        $this->numberOfRemainingMistake--;
    }

    /**
     * ログインが必要になるまでの回数をリセット
     *
     * @return void
     */
    public function resetNumberOfRemainingMistake(): void
    {
        $this->numberOfRemainingMistake = LoginSystem::getConfigArray()["wrong-password-count-to-ban"];
    }
}
