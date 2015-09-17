<?php
namespace primus\ultrafactions\event\player;

use pocketmine\event\player\PlayerEvent;
use primus\ultrafactions\Faction;
use pocketmine\Player;
use pocketmine\event\Cancellable;

class PlayerFactionJoinEvent extends PlayerEvent implements Cancellable {

	public function __construct(Player $player, $rank = 'Member', $inviter = 'Undefined'){
		$this->player = $player;
		$this->rank = $rank;
		$this->inviter = $inviter;
	}
	
	public function getPlayer(){
		return $this->player;
	}
	
	public function getRank(){
		return $this->rank;
	}
	
	public function setRank($rank){
		$this->rank = $rank;
	}
	
	public function getInviter(){
		return $this->inviter;
	}
	
	public function setInviter($inviter){
		$this->inviter = $inviter;
	}

}
