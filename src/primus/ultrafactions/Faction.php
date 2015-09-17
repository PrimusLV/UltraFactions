<?php
namespace primus\ultrafactions;

use pocketmine\Server;
use pocketmine\IPlayer;
use pocketmine\Player;

class Faction {

	protected $players, $plots, $friends, $enemies, $stats, $name, $date;
	
	public function __toString(){
		return $this->name;
	}

	public function __construct(array $players, array $plots, array $friends, array $enemies, array $stats, $name, $date){
		if(!isset($stats['kills']) || !isset($stats['deaths'])){
			$stats['kills'] = 0;
			$stats['deaths'] = 0;
		}
		
		$this->players = $players;
		$this->plots = $plots;
		$this->friends = $friends;
		$this->enemies = $enemies;
		$this->stats = $stats;
		$this->name = $name;
		$this->date = $date;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getPlayers(){
		return $this->players;
	}
	
	public function addPlayer(IPlayer $player, $rank='Member', $invitedBy = 'Undefined'){
		$this->players[$player->getName()] = array(
			"name" => $player->getName(),
		    "stats" => array(
				"kills" => 0,
				"deaths" => 0,
				),
			"invited-by" => $invitedBy,
			"joined" => date("Y:M:D h:i"),
			"rank" => $rank
		);
	}
	
	public function setPlayers(array $players){
		$this->players = $players;
	}
	
	public function getPlots(){
		return $this->plots;
	}
	
	public function setPlots(array $plots){
		$this->plots = $plots;
	}
	
	public function claimPlot($x, $z){
		$x = $x >> 4;
		$z = $z >> 4;
		$this->plots[] = $x.':'.$z;
	}
	
	public function unclaimPlot($x, $z){
		$x = $x >> 4;
		$z = $z >> 4;
		var_dump($this->plots);
		unset($this->plots[array_search($x.":".$z)]);
		/*Debug*/echo "Deleted: ".array_search($x.":".$z).' from - ';
		var_dump($this->plots);
	}
	
	public function isInPlot($x, $z){
		$x = $x >> 4;
		$z = $z >> 4;
		return in_array($x.':'.$z, $this->plots, true);
	}
	
	public function getFriends(){
		return $this->friends;
	}
	
	public function getPlayerRank(IPlayer $player){
		if($this->isMember($player)){
			return $this->getPlayers()[$player->getName()]['rank'];
		}
	}
	
	public function setPlayerRank(IPlayer $player, $rank){
		if($this->isMember($player)){
			$this->getPlayers()[$player->getName()]['rank'] = $rank;
		}
	}
	
	public function setFriends(array $friends = []){
		$this->friends = $friends;
	}
	
	public function getEnemies(){
		return $this->enemies;
	}
	
	public function setEnemies(array $enemies = []){
		$this->enemies = $enemies;
	}
	
	public function getKills(){
		return $this->stats['kills'];
	}
	
	public function setKills(int $kills){
		$this->stats['kills'] = $kills;
	}
	
	public function getDeaths(){
		return $this->stats['deaths'];
	}
	
	public function setDeaths(int $deaths){
		$this->stats['deaths'] = $deaths;
	}
	
	public function getCreateDate(){
		return $this->date;
	}
	
	public function addFriend(Faction $faction){
		if(in_array($faction->__toString(), $this->friends, true)) return false;
		$this->friends[] = $faction->__toString();
		return true;
	}
	
	public function removeFriend(Faction $faction){
		unset($this->friends[array_search($faction->__toString(), $this->friends)]);
	}
	
	public function addEnemy(Faction $faction){
		if(in_array($faction->__toString(), $this->enemies, true)) return false;
		$this->enemies[] = $faction->__toString();
		return true;
	}
	
	public function removeEnemy(Faction $faction){
		unset($this->enemies[array_search($faction->__toString(), $this->enemies)]);
	}
	
	public function isMember(IPlayer $player){
		if( array_key_exists($player->getName(), $this->players) ) return true;
		echo $player->getName()." was not in ".$this.' faction current members: '.var_dump($this->players);
		return false;
	}
	
	public function __destruct(){
		Server::getInstance()->getLogger()->info('[UltraFactions] '.$this.' destroyed due to it was not used by any player');
	}


}
