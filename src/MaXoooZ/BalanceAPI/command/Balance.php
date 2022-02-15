<?php

namespace MaXoooZ\command;

use MaXoooZ\BalanceAPI\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Balance extends Command
{
    public function __construct()
    {
        parent::__construct(
            "balance",
            "See your balance or that of another player",
            null,
            ["bal", "money"]
        );

        $this->setPermission("command.balanceapi.balance");
    }

    public function execute(CommandSender $sender, $commandLabel, array $args): void
    {
        if (empty($args[0])) {
            if ($sender instanceof Player) {
                $sender->sendMessage(Main::getInstance()->getTranslate("MY_BALANCE", [Main::getApi()->getBalance($sender->getName()), $sender->getName()]));
            }
            return;
        }

        $target = Main::getInstance()->getServer()->getPlayerByPrefix($args[0]);

        if ($target instanceof Player) {
            $sender->sendMessage(Main::getInstance()->getTranslate("TARGET_BALANCE", [Main::getApi()->getBalance($target->getName()), $target->getName()]));
        } else {
            if (Main::getApi()->exist($target)) {
                $sender->sendMessage(Main::getInstance()->getTranslate("TARGET_BALANCE", [Main::getApi()->getBalance($target), $args[0]]));
            } else {
                $sender->sendMessage(Main::getInstance()->getTranslate("PLAYER_NOT_EXIST", [$target]));
            }
        }
    }
}