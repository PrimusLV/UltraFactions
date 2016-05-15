<?php
namespace ultrafactions\data;

use pocketmine\utils\Config;
use ultrafactions\UltraFactions;

class DefaultDataProvider extends DataProvider
{
    private static $factionFolder = "";
    private static $memberFolder = "";
    /** @var  array $plots */
    private $plots;

    public function __construct(UltraFactions $plugin)
    {
        self::$factionFolder = $plugin->getDataFolder() . "/factions"; # Check if the slash is necessary
        self::$memberFolder = $plugin->getDataFolder() . "/members";
        parent::__construct($plugin);
        $this->type = "YAML (Default)";
    }

    public function getFactionData($name) : array
    {
        return (new Config(self::factionFile($name), Config::YAML))->getAll();
    }

    public static function factionFile($name)
    {
        return self::$factionFolder . "/" . strtolower(trim($name)) . ".yml";
    }

    public function getMemberData($name) : array
    {
        return (new Config(self::memberFile($name), Config::YAML))->getAll();
    }

    public static function memberFile($name)
    {
        return self::$memberFolder . "/" . strtolower(trim($name)) . ".yml";
    }

    public function setFactionData($name, array $data, $append = true)
    {
        if (!$append) @unlink(self::factionFile($name));
        new Config(self::factionFile($name), Config::YAML, $data);
    }

    public function setMemberData($name, array $data, $append = true)
    {
        if (!$append) @unlink(self::memberFile($name));
        return new Config(self::memberFile($name), Config::YAML, $data);
    }

    public function deleteFactionFile($name){
        @unlink(self::factionFile($name));
    }

    public function deleteMemberFile($name){
        @unlink(self::memberFile($name));
    }

    public function get($key)
    {
        switch ($key) {
            case 'plots':
                return $this->plots;
                break;
            default:
                break;
        }
        return null;
    }

    public function save($key, array $data)
    {
        switch ($key) {
            case 'plots':
                new Config($this->getPlugin()->getDataFolder() . "plots.yml", Config::YAML, $data);
                break;
            default:
                break;
        }
    }

    public function close()
    {
        # There is nothing to do here
        parent::close();
    }

    protected function init() : bool
    {
        # Init function for YAML data provider
        # This class will use two more files: factions.yml and members.yml
        @mkdir(self::$factionFolder);
        @mkdir(self::$memberFolder);

        $this->plots = (new Config($this->getPlugin()->getDataFolder() . "plots.yml", Config::YAML))->getAll();

        return true;
    }

}