<?php
namespace primus\ultrafactions;

use pocketmine\utils\Config;
use pocketmine\Server;

class FactionLoader extends FactionManager {
	
	public $loadedFactions;

  public function __construct(UFMain $obj){
    $factions = (new Config($obj->getDataFolder()."factions.yml", Config::YAML))->getAll();
    $loadedFactions = array();
    foreach($factions as $faction => $prefs){
      $loadedFactions[] = new Faction($prefs['players'], $prefs['plots'], $prefs['friends'], $prefs['enemies'], $prefs['stats'], $faction, $prefs['created']);
    }
	Server::getInstance()->getLogger()->info('Loaded Factions: ');
    foreach($loadedFactions as $faction){
		Server::getInstance()->getLogger()->info(' - '.\pocketmine\utils\TextFormat::GREEN.$faction);
	}
	$this->loadedFactions = (empty($loadedFactions) === true) ? $loadedFactions : array();
  }
	
  public function addFaction(Faction $faction){
	  $this->loadedFactions[$faction->__toString()] = $faction;
  }


}
