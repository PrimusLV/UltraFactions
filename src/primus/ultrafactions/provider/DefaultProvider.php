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
    
    $this->groups = new Config($this->plugin->getDataFolder() . "groups.yml", Config::YAML, []);
    $this->userDataFolder = $this->plugin->getDataFolder() . "players/";
    
    if(!file_exists($this->userDataFolder)) \mkdir($this->userDataFolder, 0777, true);
  }
  
  public function getMemberData(Member $member){
    return $this->getResource()->get($member->getPlayer()->getName());
  }
  
  public function setMemberdata(Member)
  


}
