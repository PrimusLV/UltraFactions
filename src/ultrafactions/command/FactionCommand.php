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
use pocketmine\utils\TextFormat as Text;

use ultrafactions\UltraFactions;
use ultrafactions\Faction;

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
                    # TODO
                    break;

                case 'info':
                    # TODO
                    if ( isset($args[1]) ){
                        $faction = $this->getPlugin()->getFaction($args[1]);
                        if($faction instanceof Faction){
                            $sender->sendMessage("--- Showing Faction's {$faction->getName()} info ---");
                            $sender->sendMessage("Name: ".$faction->getName());
                            $sender->sendMessage("Created: ".date("d.m.Y H:i:s", $faction->getCreationTime()));
                            $sender->sendMessage("Members: ".count($faction->getMembers()));
                            $sender->sendMessage("Leader: ".$faction->getLeader());
                            $sender->sendMessage("Bank: ".($sender->hasPermission("uf.command.info.bank") ? $faction->getBank() : "SECRET"));
                            $sender->sendMessage("Home: ".($sender->hasPermission("uf.command.info.home") ? $faction->getHome() : "SECRET"));
                            $sender->sendMessage("Plots: ".count($faction->getPlots()));
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
                    if( ($m = $this->getPlugin()->getMember($sender))->getFaction() instanceof Faction === false ){
                        $sender->sendMessage("You must be in faction to use this command");
                    }
                    # Check if player is leader or co-leader
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
     * @return \pocketmine\plugin\Plugin
     */
    public function getPlugin()
    {
        return $this->plugin;
    }
}