<?php
namespace ultrafactions\data;

use pocketmine\Player;
use ultrafactions\UltraFactions;

abstract class DataProvider
{

    protected $plugin;
    protected $valid = false;
    protected $type = "Dummy";

    public function __construct(UltraFactions $plugin)
    {
        $this->plugin = $plugin;

        $this->valid = $this->init();
    }

    /**
     * Called when provider is loading, we should load files or connect to databases in this function
     * If something in here goes wrong the provider will be useless and plugin should stop
     *
     * You can throw exceptions here and it will be catch
     */
    protected abstract function init() : bool;

    public static function playerName(Player $player)
    {
        return strtolower(trim($player->getName()));
    }

    public function isValid() : bool
    {
        return $this->valid === true;
    }

    /*

        Data related setters and getters

    */

    public abstract function getFactionData($name) : array;

    public abstract function getMemberData($name) : array;

    public abstract function setFactionData($name, array $data, $append = true);

    public abstract function setMemberData($name, array $data, $append = true);

    public abstract function get($key);

    public abstract function save($key, array $data);

    public function close()
    {
        # Call parent function to display this message :P
        $this->getPlugin()->getLogger()->info("Closed {$this->getType()} data provider.");
    }

    public function getPlugin() : UltraFactions
    {
        return $this->plugin;
    }

    public function getType() : string
    {
        return $this->type;
    }

}