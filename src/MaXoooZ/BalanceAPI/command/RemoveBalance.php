<?php

namespace MaXoooZ\command;

use JsonException;
use MaXoooZ\BalanceAPI\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class RemoveBalance extends Command
{
    public function __construct()
    {
        parent::__construct(
            "removebalance",
            "",
            null,
            ["removebal", "removemoney"]
        );

        $this->setPermission("command.balanceapi.removebalance");
    }

    /**
     * @throws JsonException
     */
    public function execute(CommandSender $sender, $commandLabel, array $args): void
    {
        if (empty($args[0]) || empty($args[1])) {
            $sender->sendMessage(Main::getInstance()->getTranslate("RM_BALANCE", []));
            return;
        } else if (!$this->testPermission($sender, $this->getPermission())) {
            return;
        }

        $target = Main::getInstance()->getServer()->getPlayerByPrefix($args[0]);

        if (!is_numeric($args[1]) || 0 > $args[1]) {
            $sender->sendMessage(Main::getInstance()->getTranslate("AMOUNT_INVALID", []));
            return;
        }

        if ($target instanceof Player) {
            $this->removeBalance($sender, $target->getName(), floor($args[1]));
        } else {
            if (Main::getApi()->exist($target)) {
                $this->removeBalance($sender, $args[0], floor($args[1]));
            } else {
                $sender->sendMessage(Main::getInstance()->getTranslate("PLAYER_NOT_EXIST", [$target]));
            }
        }
    }

    /**
     * Remove money on a player balance
     *
     * @throws JsonException
     */
    private function removeBalance(CommandSender $sender, string $target, int $balance): void
    {
        $sender->sendMessage(Main::getInstance()->getTranslate("RM_BALANCE_SUCCES", [$target, $balance]));
        Main::getApi()->addToBalance($target, -$balance);
    }
}