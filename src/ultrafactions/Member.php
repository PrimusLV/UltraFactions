<?php
/**
 * Created by PhpStorm.
 * User: primus
 * Date: 4/22/16
 * Time: 1:45 PM
 */

namespace ultrafactions;

use pocketmine\Player;

class Member
{

    const RANK_MEMBER = 0;
    const RANK_OFFICER = 1;
    const RANK_CO_LEADER = 2;
    const RANK_LEADER = 3;

    /** @var UltraFactions $plugin */
    private static $plugin;

    private static $defaultData = [
        "power" => 0,
        "stats" => [
            "kills" => 0,
            "deaths" => 0,
        ],
        "faction" => null,
        "rank" => Member::RANK_MEMBER
    ];

    protected $stats = [];
    protected $rank = Member::RANK_MEMBER;

    protected $faction;
    protected $player;

    public function __construct(Player $player, $faction, $power = 0, array $stats)
    {
        $this->player = $player;
        if (empty($stats)) {
            $this->stats = self::$defaultData['stats'];
        } else {
            $this->stats = $stats;
        }
        if ($faction != null) {
            if ($faction instanceof Faction) {
                $this->faction = $faction;
            } elseif (($f = $this->getPlugin()->getFactionManager()->getFaction($faction)) instanceof Faction) {
                $this->faction = $f;
            } else {
                $this->faction = null;
            }
        }
        $this->power = $power;

    }

    public function getPlugin() : UltraFactions
    {
        return self::$plugin;
    }

    public static function setPlugin(UltraFactions $p)
    {
        self::$plugin = $p;
    }

    /**
     * @return UltraFactions|null
     */
    public function getFaction()
    {
        return $this->faction;
    }

    public function setFaction(Faction $faction)
    {
        $this->faction = $faction;
        $faction->update();
    }

    /**
     * @return bool|void
     */
    public function leave()
    {
        if ($this->faction instanceof Faction) {
            $f = $this->faction;
            $this->faction = null;
            return $f->detachMember($this);
        }
        return false;
    }

    public function join(Faction $faction)
    {
        // Call event
        if ($this->faction === $faction or $this->isInFaction()) return false;
        return $faction->attachMember($this);
    }

    public function isInFaction()
    {
        if ($this->faction instanceof Faction) {
            return $this->faction->isMember($this);
        }
        return false;
    }

    public function getName()
    {
        return $this->getPlayer()->getName();
    }

    public function getPlayer() : Player
    {
        return $this->player;
    }

    public function save()
    {
        var_dump($this->getMemberData());
        $this->getPlugin()->getDataProvider()->setMemberData($this->getPlayer()->getName(), $this->getMemberData(), true);
    }

    public function getMemberData() : array
    {
        $nData = [];
        foreach (self::$defaultData as $key => $value) {
            if ($key == 'faction') {
                $nData['faction'] = $this->faction instanceof Faction ? $this->faction->getName() : null;
            }
            if (isset($this->{$key})) $nData[$key] = $this->{$key};
        }
        return $nData;
    }

    public function getRank() : int
    {
        return $this->rank;
    }

}