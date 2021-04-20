<?php

namespace gravity\refaltor\task;

use gravity\refaltor\gravity;
use pocketmine\level\Position;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class teleportTask extends Task
{

	private $player;
	private $timer = 5;

	public function __construct($player)
	{
		$this->player = $player;
	}

	public function onRun(int $currentTick)
	{
		$player = Server::getInstance()->getPlayer($this->player);
		if (!empty($player)){
			switch ($this->timer){
				case 5:
				case 4:
				case 3:
				case 2:
				case 1:
					$player->sendPopup("§6- §eteleportation in §c{$this->timer} §esecond(s) §6-");
					break;
				case 0:
					$level = Server::getInstance()->getLevelByName(gravity::getInstance()->getConfig()->get("world_teleport")["world"]);
					Server::getInstance()->loadLevel(gravity::getInstance()->getConfig()->get("world_teleport")["world"]);
					$level->loadChunk(gravity::getInstance()->getConfig()->get("world_teleport")["x"] >> 4, gravity::getInstance()->getConfig()->get("world_teleport")["z"] >> 4);
					$x = gravity::getInstance()->getConfig()->get("world_teleport")["x"];
					$y = gravity::getInstance()->getConfig()->get("world_teleport")["y"];
					$z = gravity::getInstance()->getConfig()->get("world_teleport")["z"];
					$player->teleport(new Position($x, $y, $z, $level));
					$player->sendPopup("§6- §eTeleportation ! §6-");
					gravity::getInstance()->getScheduler()->cancelTask($this->getTaskId());
					break;
			}
			$this->timer--;
		}else gravity::getInstance()->getScheduler()->cancelTask($this->getTaskId());
	}
}