<?php
namespace ultrafactions\faction;

use pocketmine\level\Position;
use pocketmine\Player;
use ultrafactions\Member;
use ultrafactions\UltraFactions;

class Faction
{

    /*
    *    name => is got from factions.yml
    *    displayName => is from $data
    */

    private static $plugin;

    private static $defaultData = [
        "name" => "",
        "bank" => 0,
        "home" => null,
        "allies" => [],
        "enemies" => [],
        "members" => [],
        "power" => 0,
        "plots" => [],
        "created" => 0, # time()
        "displayName" => ""
    ];

    /**
     * Members are assigned to factions not factions to members
     */

    /** @var string $name */
    protected $name = "";
    /** @var string $displayName */
    protected $displayName = "";
    /** @var int $power */
    protected $power;
    /** @var Position $home */
    protected $home = null;
    /** @var String[] $members */
    protected $members = [];
    /** @var String[] $plots */
    /** @var int $created */
    protected $created;

    /**
     * Faction constructor.
     * Faction will adapt to invalid data given and use Faction::$defaultData
     *
     * @throws \Exception
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
        $displayName = isset($data['displayName']) ? $data['displayName'] : self::$defaultData['displayName'];

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
        $this->displayName = $displayName;

        if( $this->getDisplayName() === "" ){ # Of course I could do it above but what would be the difference?
            throw new \Exception("Invalid display name");
        }
        if( $this->getLeader() === "" ){
            throw new \Exception("Faction can not exist without leader");
        }
    }

    public function getDisplayName() : string
    {
        return $this->displayName;
    }

    public function getLeader() : string 
    {
        foreach ($this->members as $member => $rank) {
            if ($rank === Member::RANK_LEADER) return $member;
        }
        return "";
    }

    public static function getDefaultData() : array
    {
        return self::$defaultData;
    }

    public static function setPlugin(UltraFactions $p)
    {
        if (!self::$plugin) self::$plugin = $p;
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

    public function getCreationTime() : int
    {
        return $this->created;
    }

    /**
     * @return Player[]
     */
    public function getOnlineMembers() : array
    {
        $m = [];
        foreach ($this->getPlugin()->getMemberManager()->getAll() as $p) {
            if ($p->getFaction() === $this) $m[] = $p;
        }
        return $m;
    }

    private function getPlugin() : UltraFactions
    {
        return self::$plugin;
    }

    public function sendMessage($message)
    {
        foreach ($this->getMembers() as $player) {
            $player->sendMessage($message);
        }
    }

    // Some functions :D

    /**
     * This returns all member's names
     *
     * @return String[]
     */
    public function getMembers() : array
    {
        return $this->members;
    }

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

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    public function getFactionData() : array
    {
        $d = [];
        foreach (self::$defaultData as $key => $value) {
            $d[$key] = $this->{$key};
        }
        return $d;
    }

    public function attachMember(Member $player, $rank = Member::RANK_MEMBER)
    {
        $this->members[strtolower($player->getName())] = $rank;
        return $rank;
    }

    public function detachMember(Member $player) : bool
    {
        unset($this->members[strtolower($player->getName())]);
        return true;
    }

    public function setMemberRank(Member $member, $rank) : bool
    {
        if ($this->isMember($member->getPlayer()->getName())) {
            $this->members[strtolower($member->getPlayer()->getName())] = $rank;
            return true;
        }
        return false;
    }

    // Integration with plugin

    /**
     * @param $name
     * @return bool
     */
    public function isMember($name) : bool
    {
        var_dump($this->members);
        if (array_key_exists(strtolower($name), $this->members)) {
            echo "Player is member of " . $this->getDisplayName() . " faction";
            return true;
        } else {
            echo "Player isn't member of " . $this->getDisplayName() . " faction";
            return false;
        }
    }

    public function newLeader(Member $member)
    {
        # TODO
    }

}