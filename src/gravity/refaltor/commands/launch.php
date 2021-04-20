<?php

namespace gravity\refaltor\commands;

use gravity\refaltor\entity\newHuman;
use gravity\refaltor\gravity;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\Player;

class launch extends Command
{
	public static $remove = [];

	public function __construct()
	{
		parent::__construct("launch", "Allows you to spawn a rocket launcher", "§cUsage: /launch <spawn:remove>");
		$this->setPermission("gravity.spawn.launch");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof Player){
			if ($sender->hasPermission("gravity.spawn.launch") || $sender->isOp()){
				if (isset($args[0])){
					if (strtolower($args[0]) === "spawn"){
						$nbt = Entity::createBaseNBT($sender, null, 1, 1);
						$nbt->setTag($sender->namedtag->getTag("Skin"));
						$launch = new newHuman($sender->getLevel(), $nbt);
						$launch->setNameTag("§eRocket");
						$launch->setImmobile(true);
						$launch->setScale(3);
						self::setSkinEntity($launch, "launch.png", "geometry.launch", "launch.geo.json", 64);
						$launch->spawnToAll();
					}elseif(strtolower($args[0]) === "remove"){
						self::$remove[$sender->getName()] = true;
						$sender->sendMessage("§aTap the rocket you want to remove");
					}
				}else $sender->sendMessage(self::getUsage());
			}else $sender->sendMessage("§4/!\ §cYou don’t have permission!");
		}
	}

	public static function setSkinEntity($entity, $image, $geometry, $json, $taille) {
		$skin =  $entity->getSkin();
		$plugin = gravity::getInstance();
		$path = $plugin->getDataFolder() . '3D/' . $image;
		$img = @imagecreatefrompng($path);
		$skinbytes = "";
		$s = (int)@getimagesize($path)[1];
		for($y = 0; $y < $s; $y++) {
			for($x = 0; $x < $taille; $x++) {
				$colorat = @imagecolorat($img, $x, $y);
				$a = (( ~ ((int)($colorat >> 24))) << 1) & 0xff;
				$r = ($colorat >> 16) & 0xff;
				$g = ($colorat >> 8) & 0xff;
				$b = $colorat & 0xff;
				$skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);

			}
		}
		@imagedestroy($img);
		$entity->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", $geometry, file_get_contents($plugin->getDataFolder() . "3D/" . $json)));
		$entity->sendSkin();
	}
}