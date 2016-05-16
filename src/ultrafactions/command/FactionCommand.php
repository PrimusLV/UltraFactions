<?php
/**
 * Created by PhpStorm.
 * User: primus
 * Date: 4/26/16
 * Time: 5:03 PM
 */

namespace ultrafactions\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat as Text;
use ultrafactions\faction\Faction;
use ultrafactions\Member;
use ultrafactions\UltraFactions;


class FactionCommand extends Command implements PluginIdentifiableCommand
{

    /** @var UltraFactions $plugin */
    private $plugin;

    public function __construct(UltraFactions $plugin)
    {
        $this->plugin = $plugin;

        parent::__construct("faction");
        $this->setAliases(['fac', 'f']);
        $this->setDescription("UltraFactions main command");
        $this->setPermission("uf.command.*");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) {
            return true;
        }

        if (isset($args[0])) {
            switch (strtolower($args[0])) {

                case 'create':
                    if(!$sender instanceof Player){
                        $sender->sendMessage("Please run this command in-game");
                        return true;
                    }
                    $member = $this->getPlugin()->getMemberManager()->getMember($sender, true);
                    if ($member->getFaction() != null) {
                        $member->getPlayer()->sendMessage("You are already in faction!"); # BadBoy
                        return true;
                    }
                    if(isset($args[1])){
                        if ($this->getPlugin()->getFactionManager()->getFactionByName($args[1]) instanceof Faction) {
                            $sender->sendMessage("Faction already exists");
                            return true;
                        }
                    # Check if player has enough money
                    $price = $this->getPlugin()->getPrice('faction.create');
                    $money = $this->getPlugin()->getEconomy()->getMoney($sender);
                    if($price != 0){
                        if($price > $money){
                            $sender->sendMessage("You don't have enough money");
                            return true;
                        }
                    }
                    # Filter name    
                    if(!ctype_alnum($args[1])){
                        $sender->sendMessage("Only numbers and letters allowed");
                        return true;
                    }
                    if(strlen($args[1]) > 8){
                        $sender->sendMessage("Name is too long");
                        return true;
                    } elseif (strlen($args[1]) < 3){
                        $sender->sendMessage("Name is too short");
                        return true;
                    }
                    /*
                    if($this->getPlugin()->isNameBanned($args[1])){
                        # TODO
                    }
                    */

                    # Create faction
                    $data = [
                        'displayName' => $args[1],
                        'members' => [$sender->getName() => Member::RANK_LEADER],
                    ];
                        if (($f = $this->getPlugin()->getFactionManager()->createFaction($args[1], $data)) instanceof Faction) {
                        $member->join($f);
                        $sender->sendMessage("Faction created");
                    } else {
                        $sender->sendMessage("Failed to create faction");
                    }

                    } else {
                        $this->sendUsage($sender, 'create');
                    }
                    break;

                case 'info':
                    # TODO
                    if ( isset($args[1]) ){
                        $faction = $this->getPlugin()->getFactionManager()->getFactionByName($args[1]);
                        if($faction instanceof Faction){
                            $sender->sendMessage("--- Showing Faction's {$faction->getName()} info ---");
                            $sender->sendMessage("Name: ".$faction->getName());
                            $sender->sendMessage("Created: ".date("d.m.Y H:i:s", $faction->getCreationTime()));
                            $sender->sendMessage("Members: ".count($faction->getMembers()));
                            $sender->sendMessage("Leader: ".$faction->getLeader());
                            $sender->sendMessage("Bank: ".($sender->hasPermission("uf.command.info.bank") ? $faction->getBank() : "SECRET"));
                            $sender->sendMessage("Home: ".($sender->hasPermission("uf.command.info.home") ? $faction->getHome() : "SECRET"));
                            $sender->sendMessage("Plots: " . count($this->getPlugin()->getPlotManager()->getPlotsByFaction($faction)));
                        } else {
                            $sender->sendMessage("Faction not found");
                        }
                    } else {
                        $sender->sendMessage("Usage: /faction info <faction>");
                    }
                    break;

                case 'claim':
                    # TODO
                    break;

                case 'invite':
                    # TODO
                    break;

                case 'accept':
                    # Accept invitation
                    # TODO
                    break;
                case 'decline':
                    # Decline invitation
                    # TODO
                    break;

                case 'kick':
                    # TODO
                    break;

                case 'leave':
                case 'quit':
                case 'exit':
                    # TODO
                    break;

                case 'motd':
                    # TODO: To be honest Idk what this does but I saw this on FactionsPro xD
                    break;

                case 'promote':
                    # TODO
                    break;

                case 'demote':
                    # TODO
                    break;

                case 'bank':
                    # TODO
                    if($sender instanceof Player === false){
                        $sender->sendMessage("Please run this command in-game");
                        return true;
                    }
                    # TODO: Check if player is leader or co-leader
                    if (isset($args[1])) {
                        switch (strtolower($args[1])) {

                            case 'withdraw':
                                # TODO
                                break;

                            case 'balance':

                                # TODO
                                break;

                            default:
                                # Unknown operation
                                # Send usage message
                                break;
                        }
                    } else {
                        # Send usage of sub-command 'bank'
                    }
                    break;

                case 'help':
                    $pages = [
                        1 => [
                            # Page 1
                            "create" => "Create new faction",
                            "info" => "See Faction's information",
                            "claim" => "Add this land to your faction",
                            "invite" => "Invite new member to your faction",
                            "accept" => "Accept invitation",
                            "decline" => "Decline invitation",
                            "kick" => "Kick member from your faction"
                        ],
                        2 => [
                            "leave" => "Leave the clan you're in",
                            "motd" => "Set faction's motd",
                            "promote" => "Promote factions member",
                            "demote" => "Demote factions member",
                            "bank" => "Use your faction's money",
                            "help" => "Displays this help page"
                            # Page 2
                        ]
                    ];
                        $page = isset($args[1]) ? $args[1] : 1;
                        if(isset($pages[$page])){
                            $sender->sendMessage("--- Showing help page $page of ".count($pages)." (/f help <page>) ---");
                            foreach($pages[$page] as $cmd => $desc){
                                $sender->sendMessage(" ".($sender->hasPermission("uf.command.".$cmd) ? Text::GREEN : Text::RED )."/".$cmd.": ".Text::RESET.$desc);
                            }
                        }
                    # TODO
                    break;

                default:
                    $sender->sendMessage("Unknown sub-command. Use '/factions help' for help.");
            }
        } else {
            $sender->sendMessage("Undefined sub-command. Use '/factions help' for help");
        }
        return false; # Invalid usage

    }

    /**
     * @return UltraFactions
     */
    public function getPlugin() : UltraFactions
    {
        return $this->plugin;
    }

    private function sendUsage($sender, $string)
    {
        # TODO
    }
}