<?php

namespace gravity\refaltor\task;

use gravity\refaltor\gravity;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;

class gravityTask extends Task
{
    private $_NAME;

    public function __construct($player)
    {
        $this->_NAME = $player;
    }

    public function onRun(int $currentTick)
    {
        $player = Server::getInstance()->getPlayerExact($this->_NAME);
        if (!empty($player))
        {
            $path = gravity::getInstance()->getDataFolder();
            $data = new Config($path . "players/" . $player->getName() . ".yml", 2);
            $time = $data->get($player->getName());
            $world = gravity::getInstance()->getConfig()->get("world");
            if (in_array($player->getLevel()->getFolderName(), $world))
            {
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 40, 1, false));
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::LEVITATION), 40, -7, false));
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP), 40, 3, false));
                if ($time <= 5 && $time >= 1) {
                    $player->sendPopup("§4[§c!§4]§e Oxygene §6§l»§r§c $time");
                    $player->getLevel()->broadcastLevelEvent($player, LevelEventPacket::EVENT_SOUND_CLICK);
                    $data->set($player->getName(), $data->get($player->getName()) - 1);
                    $data->save();
                }elseif ($time <= 0){
                    if (!$player->isAlive()){} else {
                    $player->sendPopup("§4[§c!§4]§e Oxygene §6§l»§r§4 alert");
                    $player->setHealth($player->getHealth() - 1);
                    $player->broadcastEntityEvent(ActorEventPacket::HURT_ANIMATION);
                    $player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_HURT);
                    $player->addEffect(new EffectInstance(Effect::getEffect(Effect::NAUSEA), 40, 3, false));
                    }
                }else{
                    $data->set($player->getName(), $data->get($player->getName()) - 1);
                    $data->save();
                    $player->sendPopup("§eOxygene §6§l»§r§a $time");
                }
            }
        }else gravity::getInstance()->getScheduler()->cancelTask($this->getTaskId());
    }
}