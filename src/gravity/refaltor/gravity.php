<?php

namespace gravity\refaltor;

use gravity\refaltor\events\playerListener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class gravity extends PluginBase
{
    private static $instance;

    public static function getInstance(): gravity
    {
        return self::$instance;
    }

    public function onEnable()
    {
        self::$instance = $this;
        @mkdir($this->getDataFolder() . "players/");
        $this->saveDefaultConfig();
        Server::getInstance()->getPluginManager()->registerEvents(new playerListener(), $this);
    }
}