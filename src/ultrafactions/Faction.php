<?php
namespace ultrafactions;

use pocketmine\level\Position;

class Faction
{

    private static $plugin;

    private static $defaultData = [
        "name" => null,
        "bank" => 0,
        "home" => null,
        "allies" => [],
        "enemies" => []
        # "power"
        # "plots"
    ];

    /**
     * Members are assigned to factions not factions to members
     */

    /** @var string $name */
    protected $name = "";
    /** @var int $bank */
    protected $bank = 0;
    /** @var Position $home */
    protected $home = null;
    /** @var array $allies */
    protected $allies = [];
    /** @var array $enemies */
    protected $enemies = [];
    /** @var String[] $members */
    protected $members = [];

    /**
     * Faction constructor.
     * Faction should adapt to invalid data given and use Faction::$defaultData
     *
     * @param $name
     * @param array $data
     */
    public function __construct($name, array $data)
    {
        $this->name = $name;

        if(isset($faction['']))
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function isValidFactionData(array $data) : bool
    {
        foreach (self::$defaultData as $key => $value) {
            if (!array_key_exists($key, $data)) {
                echo "Key: " . $key . " was not found in array ";
                var_dump($data);
            }
        }
        return true;
    }

    public static function getDefaultData() : array
    {
        self::$defaultData;
    }

    public static function setPlugin(UltraFactions $p)
    {
        self::$plugin = $p;
    }

    /**
     * @return int
     */
    public function getBank() : int
    {
        return $this->bank;
    }

    /**
     * @return Position
     */
    public function getHome() : Position
    {
        return $this->home;
    }

    /**
     * @param Position $home
     */
    public function setHome(Position $home)
    {
        $this->home = $home;
    }

    /**
     * @return array
     */
    public function getAllies() : array
    {
        return $this->allies;
    }

    /**
     * @return array
     */
    public function getEnemies() : array
    {
        return $this->enemies;
    }

    /**
     * @param Member $member
     * @return bool
     */
    public function isMember(Member $member) : bool
    {
        return $member->getFaction() === $this;
    }

    /**
     * When setting a relationships with factions these functions will remove it from opposite side
     *
     * # TODO: Call events
     * @param Faction $faction
     */
    public function addEnemy(Faction $faction)
    {
        if ($this->isAlly($faction)) {
            $this->removeAlly($faction);
        }
        $this->enemies[] = $faction->getName();
    }

    /**
     * @param Faction $faction
     * @return bool
     */
    public function isAlly(Faction $faction) : bool
    {
        return in_array($faction->getName(), $this->allies, true) === true;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param Faction $faction
     */
    public function removeAlly(Faction $faction)
    {
    }

    /**
     * @param Faction $faction
     */
    public function addAlly(Faction $faction)
    {
        if ($this->isEnemy($faction)) {
            $this->removeEnemy($faction);
        }
        $this->allies[] = $faction;
    }

    /**
     * @param Faction $faction
     * @return bool
     */
    public function isEnemy(Faction $faction) : bool
    {
        return in_array($faction->getName(), $this->enemies, true) === true;
    }

    /**
     * @param Faction $faction
     */
    public function removeEnemy(Faction $faction)
    {
    }

    public function sendMessage($message)
    {
        foreach ($this->getMembers() as $player) {
            $player->sendMessage($message);
        }
    }

    public function getMembers()
    {
        $m = [];
        foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $p) {
            if ($this->getPlugin()->getPlayerFaction($p) === $this) $m[] = $p;
        }
        return $m;
    }

    private function getPlugin() : UltraFactions
    {
        return self::$plugin;
    }

    // Some functions :D

    public function sendPopup($popup)
    {
        foreach ($this->getMembers() as $player) {
            $player->sendPopup($popup);
        }
    }

    public function sendTip($tip)
    {
        foreach ($this->getMembers() as $player) {
            $player->sendTip($tip);
        }
    }

    public function update()
    {
        # TODO
    }

    public function save()
    {
        $this->getPlugin()->getDataProvider()->setFactionData($this->getName(), $this->getFactionData(), true);
    }

    public function getFactionData() : array
    {
        $d = [];
        foreach (self::$defaultData as $key => $value) {
            $d[$key] = $this->{$key};
        }
        return $d;
    }

    public function attachMember(Member $player) : bool
    {
        $this->members[strtolower($player->getName())] = $player;
        return true;
    }

    public function detachMember(Member $player) : bool
    {
        unset($this->members[strtolower($player->getName())]);
        return true;
    }
}