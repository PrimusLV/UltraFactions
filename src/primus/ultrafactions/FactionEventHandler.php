<?php
# FactionEventHandler 
namespace primus\ultrafactions;

use primus\ultrafactions\message\MessageFormat;
use pocketmine\utils\TextFormat;

use pocketmine\event\Listener;

use primus\ultrafactions\event\player\PlayerFactionJoinEvent;
use primus\ultrafactions\event\player\PlayerFactionLeaveEvent;
use primus\ultrafactions\event\faction\FactionPlotClaimEvent;
use primus\ultrafactions\event\faction\FactionPlotUnlaimEvent;

use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use pocketmine\event\block\BlockBreakEvent;

use pocketmine\Player;
use pocketmine\IPlayer;

class FactionEventHandler implements Listener {

	private $owner;
	
	public function __construct(UFMain $plugin){
		$this->owner = $plugin;
	}
	
	# Events
	
	/**
	 * @param PlayerDeathEvent $event
	 * @priority high
	 * @ignoreCancelled false
	 */
	public function onDeathAndKill(PlayerDeathEvent $event){
		$victim = $event->getEntity();
		if($victim->getLastDamageCause() instanceof EntityDamageByEntityEvent){
		$attacker = $victim->getLastDamageCause()->getDamager();
		if($attacker instanceof Player){
			# There was killer for victim
			if($this->owner->isInFaction($victim)){
				# Add death for $victim
				$this->owner->getPlayerFaction($victim)->getPlayers()[$victim->getName()]['stats']['deaths'] += 1;
			}
			if($this->owner->isInFaction($attacker)){
				# Add kill for $attacker
				$this->owner->getPlayerFaction($attacker)->getPlayers()[$attacker->getName()]['stats']['kills'] += 1;
			}
			return;
		}
		}
		if($this->owner->isInFaction($victim)){
			# Add death for $victim
			$this->owner->getPlayerFaction($victim)->getPlayers()[$victim->getName()]['stats']['kills'] += 1;
			return;
		}
	}
	
	public function onBlockBreak(BlockBreakEvent $event){
		$block = $event->getBlock();
		$faction = $this->owner->isInPlot($block->getFloorX(), $block->getFloorZ());
		if($faction){
		$player = $event->getPlayer();
			if($faction->getName() !== $this->owner->getPlayerFaction($player) and !$player->hasPermission('uf.break.neutral')){
				$event->setCancelled(true);
				$player->sendMessage($this->owner->mformatter->formatMessage('This plot has been claimed by: '.TextFormat::GOLD.$faction.TextFormat::WHITE.' faction', MessageFormat::INFO));
			}
		}
	}
	
	public function onInteract(PlayerInteractEvent $event){
		$block = $event->getBlock();
		$faction = $this->owner->isInPlot($block->getFloorX(), $block->getFloorZ());
		if($faction){
		$player = $event->getPlayer();
			if($faction->getName() !== $this->owner->getPlayerFaction($player) and !$player->hasPermission('uf.break.neutral')){
				$event->setCancelled(true);
				$player->sendMessage($this->owner->mformatter->formatMessage('This plot has been claimed by: '.TextFormat::GOLD.$faction.TextFormat::WHITE.' faction', MessageFormat::INFO));
			}
		}
	}
	
	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		if($this->owner->isInFaction($player)){
			$faction = $this->owner->getPlayerFaction($player);
			$rank = $faction->getPlayerRank($player);
			$format = TextFormat::DARK_GRAY.'['.TextFormat::DARK_RED.$faction.TextFormat::DARK_GRAY.']'.TextFormat::DARK_GRAY.'['.TextFormat::DARK_RED.$rank.TextFormat::DARK_GRAY.']'.TextFormat::WHITE.' %s > %s'; # Configurable
			$event->setFormat($format);
		}else{
			echo '\nNot in faction';
		}
	}

}
