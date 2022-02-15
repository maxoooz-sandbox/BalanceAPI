<?php

namespace MaXoooZ\BalanceAPI;

use MaXoooZ\command\AddBalance;
use MaXoooZ\command\Balance;
use MaXoooZ\command\Pay;
use MaXoooZ\command\RemoveBalance;
use MaXoooZ\command\TopBalance;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    private static Main $instance;
    private static Api $api;

    /**
     * Here
     *
     * @return Main
     */
    public static function getInstance(): Main
    {
        return self::$instance;
    }

    /**
     * Return the api of BalanceAPI
     *
     * @return Api
     */
    public static function getApi(): Api
    {
        return self::$api;
    }

    protected function onEnable(): void
    {
        self::$instance = $this;
        self::$api = new Api();

        $this->saveDefaultConfig();
        self::getApi()->getFile()->reload();

        $this->getServer()->getCommandMap()->registerAll("BalanceAPI", [
            new AddBalance(),
            new Balance(),
            new Pay(),
            new RemoveBalance(),
            new TopBalance()
        ]);
    }

    /**
     * Get all the players from the richest to the poorest
     *
     * @return array
     */
    public function topBalances(): array
    {
        $file = self::getApi()->getFile()->getAll();
        asort($file);
        return $file;
    }

    /**
     * Retrieve the translation of a message
     *
     * @param string $message
     * @param array $array
     *
     * @return string
     */
    public function getTranslate(string $message, array $array): string
    {
        $lang = self::getInstance()->getConfig()->get("lang");
        $file = self::getApi()->getFile("lang/" . $lang, "yml");

        $translated = $file->get($message);

        foreach ($array as $id => $value) {
            $translated = str_replace("%" . $id . "%", $value, $translated);
        }
        return $translated;
    }

    /**
     * Convert an array into pages
     *
     * @param array $array
     * @param int|null $page
     *
     * @return array
     */
    public function arrayToMessage(array $array, ?int $page): array
    {
        $result = [];
        $pageMax = ceil(count($array) / 10);

        $min = ($page * 10) - 10;
        $count = 1;
        $max = $min + 10;

        foreach ($array as $name => $balance) {
            if ($count > $max) continue;

            if ($count > $min) {
                $result[] = $this->getTranslate("TOP_FORMAT", [$count, $name, $balance]);
            }
            $count++;
        }
        return [$pageMax, $result];
    }
}