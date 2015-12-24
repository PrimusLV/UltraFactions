<?php
namespace primus\ultrafactions\provider;

use pocketmine\Player;
use primus\ultrafactions\Member;
use primus\ultrafactions\Faction;

interface Provider {

  public function getPlayerData(Player $player);
  
  public function setPlayerData(Player $player, array $data);
  
  public function removePlayerData(Player $player);

  public function getFactionData(Faction $faction);
  
  public function setFactionData(Faction $faction, array $data);
  
  public function removeFactionData(Faction $faction);
  
  public function close();

}
