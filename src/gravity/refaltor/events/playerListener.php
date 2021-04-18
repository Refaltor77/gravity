<?php

namespace gravity\refaltor\events;

use gravity\refaltor\gravity;
use gravity\refaltor\task\gravityTask;
use pocketmine\entity\Effect;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;
use pocketmine\utils\Config;

class playerListener implements Listener
{
    public static $oxygene = [];
    private static $cooldown = [];

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $oxygene = gravity::getInstance()->getConfig()->get("oxygene");
        self::$oxygene[$player->getName()] = $oxygene;
        $time = gravity::getInstance()->getConfig()->get("O²_time_use") * 20;
        gravity::getInstance()->getScheduler()->scheduleRepeatingTask(new gravityTask($player->getName()), $time);
    }

    public function onTape(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = explode(":", gravity::getInstance()->getConfig()->get("oxygene_item"));

        if ($event->getItem()->getId() . ":" . $event->getItem()->getDamage() === $item[0] . ":" . $item[1]){
            if (!isset(self::$cooldown[$player->getName()])) {
                self::$cooldown[$player->getName()] = time() + 0.7;
                $inv = $player->getInventory();
                $inv->removeItem(Item::get($item[0], $item[1]));
                $player->getLevel()->broadcastLevelEvent($player, LevelEventPacket::EVENT_SOUND_ORB);
                $quantity = gravity::getInstance()->getConfig()->get("oxygene_add");
                $player->sendTip("§4[§c!§4]§e Oxygene §6§l»§r§a + $quantity");
                self::$oxygene[$player->getName()] = self::$oxygene[$player->getName()] + $quantity;
            }elseif (time() < self::$cooldown[$player->getName()]){
            }else unset(self::$cooldown[$player->getName()]);
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();
        $oxygene = gravity::getInstance()->getConfig()->get("oxygene_add");
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
