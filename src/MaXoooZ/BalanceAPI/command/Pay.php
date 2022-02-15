<?php

namespace MaXoooZ\command;

use JsonException;
use MaXoooZ\BalanceAPI\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Pay extends Command
{
    public function __construct()
    {
        parent::__construct(
            "pay",
            "Send a part of your sold to another player"
        );

        $this->setPermission("command.balanceapi.pay");
    }

    /**
     * @throws JsonException
     */
    public function execute(CommandSender $sender, $commandLabel, array $args): void
    {
        if ($sender instanceof Player) {
            if (empty($args[0]) || empty($args[1])) {
                $sender->sendMessage(Main::getInstance()->getTranslate("PAY_USE", []));
                return;
            }

            $target = Main::getInstance()->getServer()->getPlayerByPrefix($args[0]);

            if (!$target instanceof Player) {
                $sender->sendMessage(Main::getInstance()->getTranslate("PLAYER_NOT_EXIST", [$target]));
                return;
            } else if (!is_numeric($args[1]) || 0 > $args[1]) {
                $sender->sendMessage(Main::getInstance()->getTranslate("AMOUNT_INVALID", []));
                return;
            } else if (floor($args[1]) >= Main::getApi()->getBalance($sender->getName())) {
                $sender->sendMessage(Main::getInstance()->getTranslate("POOR_MESSAGE", [floor($args[1])]));
                return;
            }
            
            $balance = floor($args[1]);

            Main::getApi()->addToBalance($target->getName(), $balance);
            Main::getApi()->addToBalance($sender->getName(), -$balance);

            $sender->sendMessage(Main::getInstance()->getTranslate("SUCCES_SENDED", [$balance, $target->getName()]));
            $sender->sendMessage(Main::getInstance()->getTranslate("SUCCES_RECEIVED", [$balance, $sender->getName()]));

            $sender->sendMessage("§aVous avez envoyé un montant égal à §6$balance §fà §6" . $target->getName());
            $target->sendMessage("§aVous avez recu un montant d'argent égal à §6$balance §fde la part de §6" . $sender->getName());
        }
    }
}