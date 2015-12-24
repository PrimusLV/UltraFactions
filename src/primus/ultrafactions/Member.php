<?php
namespace primus\ultrafactions;

use pocketmine\Player;

class Member {
  
  /** @var Faction */
  protected $faction;
  /** @var Player */
  protected $player;
  /** @var array */
  protected $stats;
  
  public function __construct(Player $player, Faction $faction, array $stats = []){
    $this->player = $player;
    $this->faction = $faction;
    $this->stats = $stats;
  }
  
  public function getFaction(){
    return $this->faction;
  }
  
  public function setFaction(Faction $faction){
    $this->faction = $faction;
  }
  
  public function getPlayer(){
    return $this->player;
  }
  
  public function getStats(){
    return $this->stats;
  }
  
  public function setStats(array $stats){
    $this->stats = $stats;
  }
  
  public function refreshStats(){
    // TODO
  }
  
  public function destroy(){
    $this->faction = null;
    $this->stats = [];
    $this->player = null;
  }

}
