<?php

namespace gravity\refaltor;

use gravity\refaltor\events\playerListener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class gravity extends PluginBase
{
    private static $instance;
    const CONFIG = [
    	"version" => "2.0.0",
		"O²_time_use" => 1,
		"oxygene" => 40,
		"oxygene_item" => "388:0",
		"oxygene_add" => 30,
		"astronaute_armor" => [
			"helmet" => 314,
			"chestplate" => 315,
			"leggings" => 316,
			"boots" => 317
			],
		"world" => [
			"world"
			]
		];

    public static function getInstance(): gravity
    {
        return self::$instance;
    }

    public function onEnable()
    {
        self::$instance = $this;
        $this->saveDefaultConfig();
        Server::getInstance()->getPluginManager()->registerEvents(new playerListener(), $this);
        $this->fixOldConfig();
    }

    private function fixOldConfig(): void
	{
		if ($this->getConfig()->exists("version")){
			if ($this->getConfig()->get("version") !== "2.0.0")  {
				$this->getConfig()->setAll(self::CONFIG);
				$this->getConfig()->save();
			}
		}else {
			$this->getConfig()->setAll(self::CONFIG);
			$this->getConfig()->save();
		}
	}
}
