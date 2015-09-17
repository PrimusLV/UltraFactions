<?php
namespace primus\ultrafactions\command;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;

use primus\ultrafactions\UFMain;

use primus\ultrafactions\message\MessageFormat as Formatter;

class FactionCommandHandler {

	private $owner;
	
	public function __construct(UFMain $plugin){
		$this->owner = $plugin;
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if(strtolower($command->getName()) === 'f' or strtolower($command->getName()) === 'faction'){
			if(!count($args) <= 0 or !count($args) > 5){
				switch(strtolower($args[0])){
					/////////////// INFO ////////////////
					case "info":
						if($sender->hasPermission('uf.command.info')){
							if(isset($args[1])){
								$target = $this->owner->getServer()->getPlayer($args[1]);
								if($target instanceof Player){
									# Get other player info
								} else {
									$sender->sendMessage($this->owner->formatMessage('Player not found', Formatter::ERROR));
									return true;
								}
							}
							if($this->owner->isInFaction($sender)){
								# Get own info
							}
						} else { # No permission: uf.command.info
							$sender->sendMessage($this->owner->formatMessage('You dont have permission for this command', Formatter::NOPERMISSION));
							return true;
						}
					break;
					////////////// CREATE /////////////////// \Need money upgrate
					case 'create':
					if($sender->hasPermission('uf.command.create')){
						if(isset($args[1])){
								$factionName = $args[1];
							    if($this->owner->factionExists($factionName)){
									$sender->sendMessage($this->owner->mformatter->formatMessage('Faction already exists with that name'));
									return true;
								}
								if(/*MONEY*/ true){
									if($this->owner->fmanager->createFaction($factionName)){
									/*MONEY*/
									$faction = $this->owner->getFaction($factionName);
									$faction->addPlayer($sender, 'Leader', null);
									$sender->sendTip($this->owner->mformatter->formatMessage('Paid - './*MONEY*/null.''));
									$sender->sendMessage($this->owner->mformatter->formatMessage('You\'ve sucessfully created new faction: '.$faction.'', MessageFormat::SUCCESS));
									return true;
								}
								}else{
									$sender->sendMessage($this->owner->mformatter->formatMessage('You dont have enough money to create new Faction'));
									return true;
								}
						}
					}else{
						$sender->sendMessage($this->owner->mformatter->formatMessage('You dont have permission to use this command', MessageFormat::NOPERMISSION));
						return true;
					}
				break;
				//////////////// CLAIM /////////////////
				if($sender->hasPermission('uf.command.claim')){
					if($this->owner->isInFaction($sender)){
						# Is in faction
						if($this->owner->isInPlot($sender->getFloorX(), $sender->getFloorZ()) == false){
							# Plot is not claimed by anyone
							if(/* IS'NT PROTECTED? */ true){
								# Land is not protected by anything
								# FINAL
								$faction = $this->owner->getPlayerFaction($sender);
								$faction->claimPlot($sender->getFloorX(), $sender->getFloorZ());
								$sender->sendMessage($this->owner->mformatter->formatMessage('You claimed this land', MessageFormat::SUCCESS));
								return true;
							} else {
								# Land is protected
								$sender->sendMessage($this->owner->mformatter->formatMessage('You cant claim this plot', MessageFormat::NORMAL));
								return true;
							}
						} else {
							# Plot is already claimed
							$sender->sendMessage($this->owner->mformatter->formatMessage('This land is already claimed by: '.$this->owner->isInPlot($sender->getFloorX(), $sender->getFloorZ())));
							return true;
						}
					} else {
						# Not in faction 
						$sender->sendMessage($this->owner->mformatter->formatMessage('You must be in faction to do this'));
						return true;
					}
				}else{
					# No permission
					$sender->sendMessage($this->owner->mformatter->formatMessage('You dont have permission to use this command', MessageFormat::NOPERMISSION));
					return true;
				}
			break;
			////////////// INVITE ///////////////
			case "invite":
			if($sender->hasPermission('uf.command.invite')){
				
			}
			}
		}
	}

}
}
