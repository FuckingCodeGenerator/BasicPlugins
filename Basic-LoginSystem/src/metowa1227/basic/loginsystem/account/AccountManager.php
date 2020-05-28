<?php
namespace metowa1227\basic\loginsystem\account;

use metowa1227\basic\loginsystem\model\AccountModel;
use metowa1227\basic\loginsystem\plugin\LoginSystem;

class AccountManager
{
    /** @var AccountModel[] */
    private static $accounts = [];

    /** @var string */
    private static $accountDataPath;

    /**
     * 初期化
     *
     * @param LoginSystem           認証用
     * @param string $accountData   アカウントデータパス
     * @return void
     */
    public static function init(LoginSystem $_, string $accountDataPath): void
    {
        if (!file_exists($accountDataPath)) {
            touch($accountDataPath);
        }

        self::$accounts = unserialize(hex2bin(file_get_contents($accountDataPath)));
        if (!self::$accounts) {
            self::$accounts = [];
        }

        self::$accountDataPath = $accountDataPath;

        self::$accounts = self::checkAndFixAccountsData();
    }

    /**
     * アカウントデータが正常かを確認し、修正
     *
     * @return array 修正済みのデータ
     */
    private static function checkAndFixAccountsData(): array
    {
        $result = [];

        foreach (self::getAccounts() as $account) {
            if (LoginSystem::isEnabledAutoBan() && ($account->getNumberOfRemainingMistake() === -1)) {
                $account->resetNumberOfRemainingMistake();
            }

            $result[$account->getName()] = $account;
        }

        return $result;
    }

    /**
     * アカウントを登録
     *
     * @param Account $account
     * @return void
     */
    public static function registerAccount(Account $account): void
    {
        self::$accounts[$account->getName()] = $account;
    }

    /**
     * アカウントを削除
     *
     * @param string $name
     * @return void
     */
    public static function deleteAccount(string $name): void
    {
        unset(self::$accounts[$name]);
    }

    /**
     * 全アカウントを取得
     *
     * @return Account[]
     */
    public static function getAccounts(): array
    {
        return self::$accounts;
    }

    /**
     * アカウントを取得
     *
     * @param string $name
     * @return Account|null
     */
    public static function getAccount(string $name): ?Account
    {
        if (!isset(self::getAccounts()[$name])) {
            return null;
        }

        return self::getAccounts()[$name];
    }

    /**
     * アカウントが存在するか
     *
     * @param string $name
     * @return boolean
     */
    public static function existAccount(string $name): bool
    {
        return isset(self::getAccounts()[$name]);
    }

    /**
     * データを保存
     *
     * @return void
     */
    public static function saveData(): void
    {
        file_put_contents(self::$accountDataPath, bin2hex(serialize(self::$accounts)), LOCK_EX);
    }
}
