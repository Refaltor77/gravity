<?php

namespace gravity\refaltor;

use gravity\refaltor\commands\launch;
use gravity\refaltor\entity\newHuman;
use gravity\refaltor\events\playerListener;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class gravity extends PluginBase
{
    private static $instance;
    const CONFIG = [
    	"version" => "3.0.0",
		"O²_time_use" => 1,
		"oxygen" => 40,
		"oxygen_item" => "388:0",
		"oxygen_add" => 30,
		"world_teleport" => [
			"x" => 0,
			"y" => 80,
			"z" => 0,
			"world" => "world"
		],
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
        Entity::registerEntity(newHuman::class, true);
        @mkdir($this->getDataFolder() . "3D/");
        Server::getInstance()->getCommandMap()->register("launch", new launch());
        $this->saveResource("3D/launch.geo.json");
		$this->saveResource("3D/launch.png");
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