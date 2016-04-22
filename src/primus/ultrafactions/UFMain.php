<?php
namespace primus\ultrafactions;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use primus\ultrafactions\provider\DefaultProvider;
use primus\ultrafactions\provider\MySQLProvider;
use primus\ultrafactions\provider\SQLite3Provider;
use primus\ultrafactions\provider\Provider;
use primus\ultrafactions\FactionLoader;
use primus\ultrafactions\FactionManager;
use primus\ultrafactions\event\player\PlayerFactionJoinEvent;
use primus\ultrafactions\event\player\PlayerFactionLeaveEvent;
use primus\ultrafactions\message\MessageFormat;
use primus\ultrafactions\command\FactionCommandHandler;

use pocketmine\utils\TextFormat;

use pocketmine\IPlayer;
use pocketmine\Player;

class UFMain extends PluginBase {

  /** @var FactionManager $fmanager */
  public $fmanager;
  /** @var MessageFormat $mformatter */
  public $mformatter;
  /** @var Config $config */
  public $config;
  /** @var FactionCommandHandler $cmdHandler */
  public $cmdHandler;
  /** @var Provider $provider */
  protected $provider;

  public function onLoad(){}
  
  public function onEnable(){
	@mkdir($this->getDataFolder());
	$this->fmanager = new FactionManager($this);
	$this->mformatter = new MessageFormat();
	$this->cmdHandler = new FactionCommandHandler($this);
	$this->getServer()->getPluginManager()->registerEvents(new FactionEventHandler($this), $this);
	
	/**
	 * Init Config
	 */
	$this->saveDefaultConfig();
	$this->config = $this->getConfig();
	
	switch ( $this->getConfig()->get('provider-type') ) {
		case 'yaml':
			$this->provider = new DefaultProvider($this);
			$provider = 'YAML';
		break;
		case 'mysql':
			$this->provider = new MySQLProvider($this);
			$provider = 'MySQL';
		break;
		case 'sqlite3':
			$this->provider = new SQLite3($this);
			$provider = "SQLite3";
		break;
	}
  }
  public function onDisable(){
	 //if($this->getConfig()->get('destroyUnusedFactions')) $this->fmanager->deleteUnusedFactions();
	  $this->fmanager->saveFactions();
  }

  /*	  ___     _ _ _   _
   * 	 /   \   /     | |_|
   *	|  X  \ |   X  |  _
   *	|  _  | | _____| | |
   *	| | | | | |      | |
   *	|_| |_| |_|      |_|
   */
	
  /**
   * @return Provider|null
   */
   public function getProvider(){
      return $this->provider;
   }

  /**
   * @param Player $player;
   * @return Faction|null
  */
  public function getPlayerFaction(Player $player){
    foreach($this->getFactions() as $faction){
      foreach($faction->getPlayers() as $member => $prefs){
        if($member === $player->getName()) return $faction;
      }
    }
      return null;
  }

  /**
  * @param string $name
  * @return Faction|null
  */
  public function getFaction($name){
    foreach($this->getFactions() as $faction){
      if($faction->getName() == $name ) {
		  return $faction; 
		  }
    }
    return null;
  }

  /**
  * @param Player $player, Faction $faction
  * @return bool
  * @description Shortcut for Faction->addPlayer();
  */
  public function setPlayerFaction(Player $player, Faction $faction, $rank = 'Member', $invitedBy = 'Undefined') : bool {
    $ev = new PlayerFactionJoinEvent($player, $faction, $rank, $invitedBy);
    if($ev->isCancelled()) return false;
    if($faction->addPlayer($player, $ev->getRank(), $ev->getInviter())){
      return true;
    } return false;
  }

  /**
   * @return array
   * @description Returns all loaded factions
   */
  public function getFactions() : array {
    return $this->fmanager->getAllFactions();
  }

  /**
   * @param string $name
   * @return bool
   * @description Get does faction is loaded/created
   */
  public function factionExists($name) : bool {
    if($this->getFaction($name) != null) return true;
    return false;
  }
  
  /**
   * @return bool
   * @param IPlayer $player
   * @description Returns if there is player in faction with given player name
   */
  public function isInFaction(IPlayer $player) : bool {
	  foreach($this->getFactions() as $faction){
		  if($faction->isMember($player)) return true;
										  return false;
	  }
  }
  
  /**
   * @return bool
   * @param IPlayer $playerA, IPlayer $playerB
   * @description Check if players as teammates
   */
  public function isInSameFaction(IPlayer $playerA, IPlayer $playerB) : bool {
	  if($this->getPlayerFaction()->getName() === $this->getPlayerFaction()->getName()) return true;
	  return false;
  }
  
  /**
   * If player is in plot thats claimed return owner of plot else return false
   * 
   * @return false|Faction
   * @param int $x, int $z
   * @description Get plot owner
   */
   public function isInPlot($x, $z){
	   foreach($this->getFactions() as $faction){
		   if($faction->isInPlot($x, $z)) return $faction;
	   }
	   return false;
   }
   
   // This is stupid idea I must get rid of this quickly
   public function onCommand(CommandSender $sender, Command $command, $label, array $args){
	   $this->cmdHandler->onCommand($sender, $command, $label, $args);
	   return true;
   }
  
  /** // Non API part ------> // */
  // Jeesus what I am doing?
  public function test(){
	  $this->fmanager->createFaction('PrO');
	  $this->getFaction('PrO')->addPlayer($this->getServer()->getOfflinePlayer('PrimusLV'), 'Leader :)', 'Owner');
	  foreach($this->fmanager->getAllFactions() as $faction){
		  $this->getLogger()->info(TextFormat::RED.' ----------------------- ');
		  $this->getLogger()->info(TextFormat::AQUA."Faction name: ".TextFormat::GOLD.$faction);
		  $this->getLogger()->info(TextFormat::AQUA."".(empty($faction->getFriends())) == true ? "Players: []" : "Players: ");
		   foreach($faction->getPlayers() as $name => $prefs){
			   $this->getLogger()->info(' - '.TextFormat::BLUE.$name.' ');
		   }
		  $this->getLogger()->info(TextFormat::AQUA.'Friends: '.(empty($faction->getFriends())) == true ? "Players: []" : "Players: ");
		  foreach($faction->getFriends() as $friend){ $this->getLogger()->info(TextFormat::AQUA.' - '.TextFormat::WHITE.$friend); }
		  $this->getLogger()->info(TextFormat::AQUA.'Enemies: '.(empty($faction->getFriends())) == true ? "Players: []" : "Players: ");
		  foreach($faction->getEnemies() as $enemy){ $this->getLogger()->info(TextFormat::BLUE.$enemy); }
	      $this->getLogger()->info(TextFormat::AQUA.'Stats: ');
	       $this->getLogger()->info(TextFormat::AQUA.' - Kills: '.TextFormat::WHITE.$faction->getKills().' ');
	       $this->getLogger()->info(TextFormat::AQUA.' - Deaths: '.TextFormat::WHITE.$faction->getDeaths().' ');
	      $this->getLogger()->info(TextFormat::AQUA.'Created: '.TextFormat::WHITE.$faction->getCreateDate().' ');
	      $this->getLogger()->info(TextFormat::RED.' ----------------------- ');
	  }
  }

}
