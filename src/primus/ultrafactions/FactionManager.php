<?php
namespace primus\ultrafactions;

	use pocketmine\Server;
	use pocketmine\Player;
	use pocketmine\utils\Config;

class FactionManager {

	public $plugin;
	public $loader;
	
	public function __construct(UFMain $plugin){
		$this->plugin = $plugin;
		$this->loadFactions();
	}

	public function getAllFactions(){
		return $this->loader->loadedFactions;
	}
	/**
	 * @api
	 * @return void
	 * @description Use with care, else all factions will be deleted.
	 */
	public function setAllFactions(array $factions = []){
		$this->loader->loadedFactions = $factions;
	}
	
	private function loadFactions(){
		$this->loader = new FactionLoader($this->plugin);
	}
	
	public function createFaction($name, array $players = [], array $friends = [], array $enemies = [], array $stats = [], array $plots = [], $date = null){
		$faction = new Faction($players, $plots, $friends, $enemies, $stats, $name, $date = ($date === null) ? date("d.j.y G:s") : $date);
		$this->loader->addFaction($faction);
		return true;
	}
	
	public function saveFactions(){
		$factions = array();
		foreach($this->getAllFactions() as $faction){
		$factions[$faction->__toString()] = array(
			"players" => $faction->getPlayers(),
			"plots" => $faction->getPlots(),
			"stats" => array(
				"kills" => $faction->getKills(),
				"deaths" => $faction->getDeaths()
				),
			"friends" => $faction->getFriends(),
			"enemies" => $faction->getEnemies(),
			"created" => $faction->getCreateDate()
		);
		}
		new Config ($this->plugin->getDataFolder()."factions.yml", Config::YAML, $factions);
		$this->plugin->getLogger()->notice('All Factions has been saved');
	}
	
	public function deleteUnusedFactions(){
		foreach($this->getAllFactions() as $faction){
			if(empty($faction->getPlayers())){
				unset($this->loader->loadedFactions[$faction->__toString()]);
				unset($faction);
			}
		}
	}

}
