<?php
namespace primus\ultrafactions\provider;

use pocketmine\Player;
use pocketmine\utils\Config;
use primus\ultrafactions\UFMain;

class DefaultProvider implements Provider {

  /** @var Config */
  protected $resource;
  
  public function __construct(UFMain $plugin){
    $this->plugin = $plugin;
    
    $this->plugin->saveResource("groups.yml");
    
    $this->factions = new Config($this->plugin->getDataFolder() . "groups.yml", Config::YAML, []);
    $this->memberDataFolder = $this->plugin->getDataFolder() . "members/";
    
    if(!file_exists($this->userDataFolder)) \mkdir($this->userDataFolder, 0777, true);
  }
  
  public function getMemberData(Member $member){
    return $this->getResource()->get($member->getPlayer()->getName());
  }
  
  public function setMemberData(Member $member, array $data){
    $this->getResource()->set($member->getPlayer()->getName(), $data);
  }
  
  public function removeMemberData(Member $member){
    $this->getResource()->set($member->getPlayer()->getName(), []);
  }
  
  // Factions
  
  public function getFactionData(Faction $faction){
    return $this->getResource()->get($faction->getName());
  }
  
  public function setFactionData(Faction  $faction){
    $this->getResource()->set($faction->getName(), $data);
  }

  public function removeFactionData(Faction $faction){
    $this->getResource()->set($faction->getName(), []);
  }
  
  public function close(){
    $this->getResource()->save();
  }
  
  public function getResource(){
    return $this->resource;
  }
}
