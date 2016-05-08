<?php
namespace ultrafactions;

use pocketmine\level\Position;

class Faction
{

    /*
        name => is got from factions.yml
        displayName => is from $data
    */

    private static $plugin;

    private static $defaultData = [
        "name" => null,
        "bank" => 0,
        "home" => null,
        "allies" => [],
        "enemies" => [],
        "members" => [],
        "power" => 0,
        "plots" => [],
        "created" => 0, # time()
        "displayName" => null
    ];

    /**
     * Members are assigned to factions not factions to members
     */

    /** @var string $name */
    protected $name = "";
    /** @var int $bank */
    protected $bank = 0;
    /** @var int $power */
    protected $power;
    /** @var Position $home */
    protected $home = null;
    /** @var array $allies */
    protected $allies = [];
    /** @var array $enemies */
    protected $enemies = [];
    /** @var String[] $members */
    protected $members = [];
    /** @var String[] $plots */
    protected $plots = [];
    /** @var int $created */
    protected $created;

    /**
     * Faction constructor.
     * Faction will adapt to invalid data given and use Faction::$defaultData
     *
     * @param $name
     * @param array $data
     */
    public function __construct($name, array $data)
    {
        $this->name = $name;

        // Fill the gaps in the array
        $bank = isset($data['bank']) ? $data['bank'] : self::$defaultData['bank'];
        $home = isset($data['home']) ? $data['home'] : self::$defaultData['home'];
        $allies = isset($data['allies']) ? $data['allies'] : self::$defaultData['allies'];
        $enemies = isset($data['enemies']) ? $data['enemies'] : self::$defaultData['enemies'];
        $members = isset($data['members']) ? $data['members'] : self::$defaultData['members'];
        $power = isset($data['power']) ? $data['power'] : self::$defaultData['power'];
        $plots = isset($data['plots']) ? $data['plots'] : self::$defaultData['plots'];
        $created = isset($data['created']) ? $data['created'] : time();

        // Convert some data to right instances
        # TODO: Lazy + this isn't urgent :P

        $this->bank = $bank;
        $this->power = $power;
        $this->home = $home;
        $this->allies = $allies;
        $this->enemies = $enemies;
        $this->members = $members;
        $this->plots = $plots;
        $this->created = $created;

        if( $this->getLeader() === "" ){
            throw new \Exception("Faction can not exist without leader");
        }
    }

    public static function getDefaultData() : array
    {
        self::$defaultData;
    }

    /**
     * Should be called more than once.
     *
     * @param UltraFactions $p
     */
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

    public function getPower() : int
    {
        return $this->power;
    }

    /**
     * @return Position|null
     */
    public function getHome()
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
     * @return array
     */
    public function getPlots() : array
    {
        return $this->plots;
    }

    /**
     * @param $name
     * @return bool
     */
    public function isMember($name) : bool
    {
        return in_array(strtolower($name), $this->members, true);
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

    public function getCreationTime() : int
    {
        return $this->created;
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
        $player->setFaction($this);
        $player->setRank(Member::RANK_MEMBER);
        return true;
    }

    public function detachMember(Member $player) : bool
    {
        unset($this->members[strtolower($player->getName())]);
        return true;
    }

    public function getLeader() : string 
    {
        foreach($this->members as $member => $rank)
        {
            if($rank === Member::RANK_LEADER) return $member;
        }     
        return "";
    }

    public function setLeader(Member $member) : bool {
        if($this->isMember($member)){
            $this->leader = $member->getName();
            $member->setRank(Member::RANK_LEADER);
        }
        return false;
    }

}