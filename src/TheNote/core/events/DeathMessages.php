<?php

//   ╔═════╗╔═╗ ╔═╗╔═════╗╔═╗    ╔═╗╔═════╗╔═════╗╔═════╗
//   ╚═╗ ╔═╝║ ║ ║ ║║ ╔═══╝║ ╚═╗  ║ ║║ ╔═╗ ║╚═╗ ╔═╝║ ╔═══╝
//     ║ ║  ║ ╚═╝ ║║ ╚══╗ ║   ╚══╣ ║║ ║ ║ ║  ║ ║  ║ ╚══╗
//     ║ ║  ║ ╔═╗ ║║ ╔══╝ ║ ╠══╗   ║║ ║ ║ ║  ║ ║  ║ ╔══╝
//     ║ ║  ║ ║ ║ ║║ ╚═══╗║ ║  ╚═╗ ║║ ╚═╝ ║  ║ ║  ║ ╚═══╗
//     ╚═╝  ╚═╝ ╚═╝╚═════╝╚═╝    ╚═╝╚═════╝  ╚═╝  ╚═════╝
//   Copyright by TheNote! Not for Resale! Not for others
//

namespace TheNote\core\events;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\particle\BlockBreakParticle;
use TheNote\core\Main;

class DeathMessages implements Listener
{

	private Main $plugin;

	public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onPlayerDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getNameTag();
        //$this->Lightning($event->getPlayer());
        if ($player instanceof Player) {
            $cause = $player->getLastDamageCause();
            $config = new Config($this->plugin->getDataFolder() . Main::$setup . "settings.json", Config::JSON);

            if ($cause->getCause() == EntityDamageEvent::CAUSE_CONTACT) {
                $event->setDeathMessage($name . $config->get("zuhoch"));
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
                $event->setDeathMessage($name . $config->get("entityattacke"));
                $this->plugin->addStrike($player);
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_PROJECTILE) {
                $event->setDeathMessage($name . $config->get("abgeschossen"));
                $this->plugin->addStrike($player);
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_SUFFOCATION) {
                $event->setDeathMessage($name . $config->get("erstickte"));
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_FIRE) {
                $event->setDeathMessage($name . $config->get("verbrannteimstehen"));
                $this->plugin->addStrike($player);
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_FIRE_TICK) {
                $event->setDeathMessage($name . $config->get("verbrannte"));
                $this->plugin->addStrike($player);
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_LAVA) {
                $event->setDeathMessage($name . $config->get("lava"));
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_DROWNING) {
                $event->setDeathMessage($name . $config->get("ertrinken"));
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION || $cause->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION) {
                $event->setDeathMessage($name . $config->get("hochgejagt"));
                $this->plugin->addStrike($player);
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_VOID) {
                $event->setDeathMessage($name . $config->get("void"));
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_SUICIDE) {
                $event->setDeathMessage($name . $config->get("selbstmord"));
                $this->plugin->addStrike($player);
            } elseif ($cause->getCause() == EntityDamageEvent::CAUSE_MAGIC) {
                $event->setDeathMessage($name . $config->get("magic"));
            }
        }
        return true;
    }
	public function Lightning(Player $player): void
	{
		$pos = $player->getPosition();
		$light = new AddActorPacket();
		$light->type = "minecraft:lightning_bolt";
		$light->actorRuntimeId = 1;
		$light->metadata = [];
		$light->motion = null;
		$light->yaw = $player->getLocation()->getYaw();
		$light->pitch = $player->getLocation()->getPitch();
		$light->position = new Vector3($pos->getX(), $pos->getY(), $pos->getZ());
		$block = $player->getWorld()->getBlock($player->getPosition()->floor()->down());
		$particle = new BlockBreakParticle($block);
		$player->getWorld()->addParticle($pos->asVector3(), $particle, $player->getWorld()->getPlayers());

		$sound = new PlaySoundPacket();
		$sound->soundName = "ambient.weather.thunder";
		$sound->x = $pos->getX();
		$sound->y = $pos->getY();
		$sound->z = $pos->getZ();
		$sound->volume = 1;
		$sound->pitch = 1;
		Server::getInstance()->broadcastPackets($player->getWorld()->getPlayers(), [$light, $sound]);
	}
}