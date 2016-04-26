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
            switch (strtolower($args[1])) {

                case 'create':
                    # TODO
                    break;

                case 'info':
                    # TODO
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
                    # TODO
                    break;

                default:
                    $sender->sendMessage("Unknown sub-command. Use '/factions help' for help.");
            }
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