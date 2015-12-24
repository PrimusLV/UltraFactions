<?php
namespace primus\ultrafactions\provider;

use pocketmine\Player;
use primus\ultrafactions\Member;
use primus\ultrafactions\Faction;

interface Provider {

  public function getMemberData(Player $player);
  
  public function setMemberData(Player $player, array $data);
  
  public function removeMemberData(Player $player);

  public function getFactionData(Faction $faction);
  
  public function setFactionData(Faction $faction, array $data);
  
  public function removeFactionData(Faction $faction);
  
  public function close();

}
