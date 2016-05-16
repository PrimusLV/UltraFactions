<?php
namespace ultrafactions\faction;

use pocketmine\level\Position;
use ultrafactions\Member;
use ultrafactions\UltraFactions;


class FactionBuilder
{
    // Error codes
    const RET_DUPLICATE = 001; // Name already taken
    const RET_INVALID_NAME = 002; // Name is invalid
    const RET_INVALID_LEADER = 003; // Invalid leader

    /** @var UltraFactions $plugin */
    private static $plugin;

    private static $defaultData = [
        "name" => "",
        "home" => null,
        "members" => [],
        "power" => 0,
        "created" => 0, # time()
        "displayName" => ""
    ];

    /** @var string $name */
    public $name = "";
    /** @var string $displayName */
    public $displayName = "";
    /** @var int $power */
    public $power;
    /** @var Position $home */
    public $home = null;
    /** @var String[] $members */
    public $members = [];
    /** @var int $created */
    public $created;
    /**
     * Do not use this variable to set the leader it's for internal purposes only
     *
     * @var null|string $leader
     */
    private $leader;

    /**
     * FactionBuilder constructor.
     */
    public function __construct($name = null, $displayName = null, $leader = null)
    {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->leader = $leader;
    }

    /**
     * @param UltraFactions $plugin
     */
    public static function setPlugin(UltraFactions $plugin)
    {
        if (self::$plugin === null) self::$plugin = $plugin;
    }

    /**
     * @param string $name
     * @return FactionBuilder
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $displayName
     * @return FactionBuilder
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @param int $power
     * @return FactionBuilder
     */
    public function setPower($power)
    {
        $this->power = $power;
        return $this;
    }

    /**
     * @param Position $home
     * @return FactionBuilder
     */
    public function setHome($home)
    {
        $this->home = $home;
        return $this;
    }

    /**
     * @param String[] $members
     * @return FactionBuilder
     */
    public function setMembers($members)
    {
        $this->members = $members;
        return $this;
    }

    /**
     * @param string $player
     * @param int $rank
     * @return FactionBuilder
     */
    public function addMember($player, $rank = Member::RANK_MEMBER)
    {
        $this->members[$player] = $rank;
        return $this;
    }

    public function setLeader($player)
    {
        $name = $player instanceof Player ? $player->getName() : $player;
        $this->leader = $name; // Yes, this should be here
        $this->members[strtolower($name)] = Member::RANK_LEADER;
    }

    public function fromArray(array $data)
    {
        foreach ($data as $key => $item) {
            if (!isset($this->{$key})) continue;
            $this->{$key} = $item;
        }
    }

    /**
     * Returns created faction on success or null on failure
     *
     * @return Faction|null
     */
    public function build()
    {
        if ($this->isValidData()) {
            $faction = new Faction($this->name, $this->toArray());
            $this->getPlugin()->getFactionManager()->addFaction($faction);
            return $this->getPlugin()->getFactionManager()->getFactionByName($this->name);
        }
        return null;
    }

    /**
     * @return int
     */
    public function isValidData()
    {
        # Check if faction with that name doesn't exist
        # Check if name is valid
        # Make sure leader is valid
        # ...
        if ($this->getPlugin()->getFactionManager()->getFactionByName($this->name) instanceof Faction) return self::RET_DUPLICATE;
        if (/** Check if name is valid */
        !true
        ) return self::RET_INVALID_NAME;

        // Check if leader is valid
        if (($leader = $this->leader) != null) {
            /*if(($player = $this->getPlugin()->getServer()->getPlayerExact($leader)) instanceof \pocketmine\Player){
                # Player is online
                $member = $this->getPlugin()->getMemberManager()->getMember($player);
            } else {
                # Player is offline :/
                $member = $this->getPlugin()->getMemberManager()->getOfflineMember($leader);
            }
            if($member->getFaction() instanceof Faction) return self::RET_LEADER_IN_FACTION; */
            if ($this->getPlugin()->getMemberManager()->isMember($leader)) return self::RET_INVALID_LEADER;
        } else {
            return self::RET_INVALID_LEADER;
        }
        return true;
    }



    // Get & Set main class

    /**
     * @return UltraFactions
     */
    public function getPlugin()
    {
        return self::$plugin;
    }

    public function toArray() : array
    {
        $d = [];
        foreach (self::$defaultData as $key => $item) {
            $d[$key] = $this->{$key};
        }
        return $d;
    }
}