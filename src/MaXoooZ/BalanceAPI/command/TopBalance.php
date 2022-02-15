<?php

namespace MaXoooZ\BalanceAPI\command;

use MaXoooZ\BalanceAPI\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class TopBalance extends Command
{
    public function __construct()
    {
        parent::__construct(
            "topbalance",
            "Sees the richest of the servers",
            null,
            ["topbal", "topmoney"]
        );

        $this->setPermission("command.balanceapi.topbalance");
    }

    public function execute(CommandSender $sender, $commandLabel, array $args): void
    {
        $sort = Main::getInstance()->topBalances();
        $page = (empty($args[1])) ? 1 : $args[1];

        $response = (empty($args[1])) ? Main::getInstance()->arrayToMessage($sort, 1) : Main::getInstance()->arrayToMessage($sort, (int)$args[1]);
        $sender->sendMessage(Main::getInstance()->getTranslate("TOP_MESSAGE", [$page, $response[0]]));

        foreach ($response[1] as $command) {
            $sender->sendMessage($command);
        }
    }
}