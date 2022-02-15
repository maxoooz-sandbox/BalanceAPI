<?php

namespace MaXoooZ\BalanceAPI;

use JsonException;
use pocketmine\utils\Config;

class Api
{
    /**
     * Get the balance of a player
     *
     * @param string $player
     * @return int
     */
    public function getBalance(string $player): int
    {
        $file = $this->getFile();

        if ($file->exists(strtolower($player))) {
            return intval($file->get($player));
        } else {
            return intval(Main::getInstance()->getConfig()->get("default-balance"));
        }
    }

    /**
     * Adds money to a player's balance
     *
     * @param string $player
     * @param int $amount
     *
     * @return void
     * @throws JsonException
     */
    public function addToBalance(string $player, int $amount): void
    {
        $this->setBalance(
            $player,
            $amount + $this->getBalance($player)
        );
    }

    /**
     * Set balance of a player
     *
     * @param string $player
     * @param int $amount
     *
     * @return void
     * @throws JsonException
     */
    public function setBalance(string $player, int $amount): void
    {
        $file = self::getFile();

        $file->set(strtolower($player), $amount);
        $file->save();
    }

    /**
     * Search if the player has already been registered
     *
     * @param string $target
     * @return bool
     */
    public function exist(string $target): bool
    {
        $file = self::getFile();

        if ($file->exists(strtolower($target))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieve the default player file or the files listing the translations
     *
     * @param string $file
     * @param string|null $type
     *
     * @return Config|null
     */
    public function getFile(string $file = "db", string $type = null): ?Config
    {
        if (is_null($type)) {
            $type = "yml";
        } else {
            $type = strtolower(Main::getInstance()->getConfig()->get("provider"));
        }

        if (!in_array($type, ["json", "yml"])) {
            Main::getInstance()->getLogger()->notice("The $type provider does not exist");
            Main::getInstance()->getServer()->getPluginManager()->disablePlugin(Main::getInstance());
            return null;
        }

        return new Config(Main::getInstance()->getDataFolder() . $file . "." . $type);
    }
}