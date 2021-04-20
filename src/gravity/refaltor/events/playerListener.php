<?php

namespace gravity\refaltor\events;

use gravity\refaltor\commands\launch;
use gravity\refaltor\forms\SimpleForm;
use gravity\refaltor\gravity;
use gravity\refaltor\task\gravityTask;
use gravity\refaltor\task\teleportTask;
use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;

class playerListener implements Listener
{
    public static $oxygene = [];
    private static $cooldown = [];
    private static $timer = 5;

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $oxygene = gravity::getInstance()->getConfig()->get("oxygen");
        self::$oxygene[$player->getName()] = $oxygene;
        $time = gravity::getInstance()->getConfig()->get("O²_time_use") * 20;
        gravity::getInstance()->getScheduler()->scheduleRepeatingTask(new gravityTask($player->getName()), $time);
    }

    public function onBreak(BlockBreakEvent $event){
    	if (isset(launch::$remove[$event->getPlayer()->getName()])){
    		$event->setCancelled(true);
		}
	}

    public function onTape(EntityDamageByEntityEvent $event){
    	$launch = $event->getEntity();
    	$damager = $event->getDamager();
    	if ($damager instanceof Player){
    		if (isset(launch::$remove[$damager->getName()])){
				if ($launch->getNameTag() === "§eRocket"){
					unset(launch::$remove[$damager->getName()]);
					$launch->flagForDespawn();
					$damager->sendMessage("§aYou have just successfully removed the rocket!");
				}
			}
		}
    	if (!$launch instanceof Player){
    		if ($launch->getNameTag() === "§eRocket"){
				$event->setCancelled(true);
				if (!isset(launch::$remove[$damager->getName()])){
					if ($damager instanceof Player) self::worldUI($damager);
				}
			}
		}

	}

	public function worldUI(Player $player){
    	$name = $player->getName();
    	$config = gravity::getInstance()->getConfig();
    	$form = new SimpleForm(function (Player $player, $data = null) use ($name, $config) {
    		if (is_null($data)) return;
    		switch ($data){
				case 0:
					gravity::getInstance()->getScheduler()->scheduleRepeatingTask(new teleportTask($player->getName()), 20);
					break;
			}
		});
    	$form->setTitle("§ateleportation");
    	$form->setContent("§8Welcome to the interface to teleport to a §c§llunar §r§8environment !");
    	$form->addButton("§l§cteleportation");
    	$form->addButton("Back");
    	$player->sendForm($form);
	}

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = explode(":", gravity::getInstance()->getConfig()->get("oxygen_item"));

        if ($event->getItem()->getId() . ":" . $event->getItem()->getDamage() === $item[0] . ":" . $item[1]){
            if (!isset(self::$cooldown[$player->getName()])) {
                self::$cooldown[$player->getName()] = time() + 0.7;
                $inv = $player->getInventory();
                $inv->removeItem(Item::get($item[0], $item[1]));
                $player->getLevel()->broadcastLevelEvent($player, LevelEventPacket::EVENT_SOUND_ORB);
                $quantity = gravity::getInstance()->getConfig()->get("oxygen_add");
                $player->sendTip("§4[§c!§4]§e Oxygen §6§l»§r§a + $quantity");
                self::$oxygene[$player->getName()] = self::$oxygene[$player->getName()] + $quantity;
            }elseif (time() < self::$cooldown[$player->getName()]){
            }else unset(self::$cooldown[$player->getName()]);
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();
        $oxygene = gravity::getInstance()->getConfig()->get("oxygen_add");
        self::$oxygene[$player->getName()] = $oxygene;
    }


    public function onDamage(EntityDamageEvent $event)
    {
        $player = $event->getEntity();
        if ($player instanceof Player)
        {
            if ($event->getCause() === EntityDamageEvent::CAUSE_FALL){
                if ($player->hasEffect(Effect::LEVITATION)){
                    $event->setCancelled(true);
                }
            }
        }
    }
}