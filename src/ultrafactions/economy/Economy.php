<?php
# Economy
namespace ultrafactions\economy;

use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class Economy {
	
	private $economy, $owner;

    public function __construct(Plugin $plugin, $preferred)
    {
		$this->owner = $plugin;
	        $economy = ["EconomyAPI", "PocketMoney", "MassiveEconomy", "GoldStd"];
	        $ec = [];
	        foreach($economy as $e){
	            $ins = $plugin->getServer()->getPluginManager()->getPlugin($e);
	            if($ins instanceof Plugin && $ins->isEnabled()){
	                $ec[$ins->getName()] = $ins;
	            }
	        }
        if (isset($ec[$preferred])) {
            $this->economy = $ec[$preferred];
	        } else {
	        	if(!empty($ec)){
	        		$this->economy = $ec[array_rand($e)];
	        	}
	        }
	        if($this->isLoaded()){
	        	$this->owner->getLogger()->info("Economy plugin: ".TextFormat::GOLD."".$this->getName());
	        }
	}

    public function isLoaded() : bool
    {
        return $this->economy instanceof Plugin;
    }

    public function getName() : String
    {
        return $this->economy->getDescription()->getName();
    }

    public function takeMoney(Player $player, $amount, $force = false)
    {
		if($this->getName() === 'EconomyAPI'){
            return $this->economy->reduceMoney($player, $amount, $force);
		}
		if($this->getName() === 'PocketMoney'){
            return $this->economy->grantMoney($player, $amount, $force);
		}
		if($this->getName() === 'GoldStd'){
            return $this->economy->grantMoney($player, $amount, $force); // CHECK
		}
		if($this->getName() === 'MassiveEconomy'){
            return $this->economy->takeMoney($player, $amount, $force);
		}
		return false;
	}

    public function getMoney(Player $player) : int
    {
		if($this->getName() === 'EconomyAPI'){
			return $this->economy->myMoney($player);
		}
		if($this->getName() === 'PocketMoney'){
			return $this->economy->getMoney($player->getName());
		}
		if($this->getName() === 'GoldStd'){
			return $this->economy->getMoney($player); // Check
		}
		if($this->getName() === 'MassiveEconomy'){
            if ($this->economy->isPlayerRegistered($player->getName())) {
                return $this->economy->getMoney($player->getName());
            }
		}
        return 0;
	}

    public function formatMoney($amount) : string
    {
		if($this->getName() === 'EconomyAPI'){
            return $this->getMonetaryUnit() . $amount;
		}
		if($this->getName() === 'PocketMoney'){
            return $amount . ' ' . $this->getMonetaryUnit();
		}
		if($this->getName() === 'GoldStd'){
            return $amount . $this->getMonetaryUnit();
		}
		if($this->getName() === 'MassiveEconomy'){
            return $this->getMonetaryUnit() . $amount;
		}
        return $amount;
	}

    public function getMonetaryUnit() : string
    {
		if($this->getName() === 'EconomyAPI'){
            return $this->economy->getMonetaryUnit();
		}
		if($this->getName() === 'PocketMoney'){
            return 'PM';
		}
		if($this->getName() === 'GoldStd'){
            return 'G';
		}
		if($this->getName() === 'MassiveEconomy'){
            return $this->economy->getMoneySymbol() != null ? $this->economy->getMoneySymbol() : '$';
		}
        return "";
	}

    public function getApi() : Plugin
    {
        return $this->economy;
	}
}