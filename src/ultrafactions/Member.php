<?php
/**
 * Created by PhpStorm.
 * User: primus
 * Date: 4/22/16
 * Time: 1:45 PM
 */

namespace ultrafactions;

use pocketmine\Player;
use ultrafactions\faction\Faction;

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
        "faction" => null,
        "rank" => Member::RANK_MEMBER
    ];

    protected $stats = [];
    protected $rank = Member::RANK_MEMBER;

    protected $faction;
    protected $player;

    public function __construct(Player $player, $faction, $power = 0)
    {
        $this->player = $player;

        if ($faction != null) {
            if ($faction instanceof Faction) {
                $this->faction = $faction;
            } elseif (($f = $this->getPlugin()->getFactionManager()->getFactionByName($faction)) instanceof Faction) {
                $this->faction = $f;
            } else {
                $this->faction = null;
            }
        } else {
            $this->faction = null;
        }
        $this->power = $power;

    }

    private function getPlugin() : UltraFactions
    {
        return self::$plugin;
    }

    public static function setPlugin(UltraFactions $p)
    {
        if (!self::$plugin) self::$plugin = $p;
    }

    /**
     * @return UltraFactions|null
     */
    public function getFaction()
    {
        return $this->faction;
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
        // TODO: Call event
        if ($this->faction === $faction or $this->isInFaction()) return false;
        $rank = $faction->attachMember($this);
        if ($rank === false) return false;
        $this->faction = $faction;
        $this->rank = $rank;
        return true;
    }

    public function isInFaction()
    {
        if ($this->faction instanceof Faction) {
            return true;
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

    // Integration with plugin

    public function getRank() : int
    {
        return $this->rank;
    }

    public function setRank($rank)
    {
        $this->rank = $rank;
    }

}