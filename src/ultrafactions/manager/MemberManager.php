<?php
/**
 * Created by PhpStorm.
 * User: primus
 * Date: 4/25/16
 * Time: 6:57 PM
 */

namespace ultrafactions\manager;

use pocketmine\Player;
use ultrafactions\data\DataProvider;
use ultrafactions\Faction;
use ultrafactions\Member;

class MemberManager extends Manager
{

    /** @var Member[] $members */
    private $members = [];

    public function __construct()
    {
        $this->init();
    }

    protected function init() : bool
    {
        return parent::init();
    }

    public function getMember(Player $player, $register = false)
    {
        if(isset($this->members[strtolower($player->getName())])){
            return $this->members[strtolower($player->getName())];
        } else {
            if ($register) {
                $this->registerPlayer($player);
            }
        }
        return null;
    }

    /**
     * Before players can use UltraFaction features they have to be registered
     *
     * @param Player $player
     * @return bool
     */
    public function registerPlayer(Player $player)
    {
            $memberD = $this->getPlugin()->getDataProvider()->getMemberData(DataProvider::playerName($player));
            $power = isset($memberD['power']) ? $memberD['power'] : 0;
            $stats = isset($memberD['stats']) ? $memberD['stats'] : array();
            $faction = null;
            if(isset($memberD['faction'])) {
                try {
                    $faction = $this->getPlugin()->getFaction($memberD['faction']);
                    if (!$faction INSTANCEOF Faction) {
                        $faction = null;
                        throw new \Exception("Member is in invalid faction");
                    }
                } catch (\Exception $e) {
                    $this->getPlugin()->getLogger()->warning("Following error occurred while registering player: " . $e->getMessage());
                }
            }
            $m = new Member($player, $faction, $power, $stats);
            $this->members[strtolower($player->getName())] = $m;
            return $m;
    }

    public function isMember(Player $player) : bool
    {
        foreach ($this->getPlugin()->getFactionManager()->getAll() as $faction) {
            if ($faction->isMember($player->getName())) return true;
        }
        return false;
    }

    public function getMembers() : array {
        return $this->members;
    }

    public function close()
    {
        $this->save();
        $this->getPlugin()->getLogger()->debug("Closed MemberManager.");
    }

    public function save(){
        foreach($this->members as $member){
            $member->save();
            $this->getPlugin()->getLogger()->debug("Saved member's '{$member->getName()}' data");
        }
            $this->getPlugin()->getLogger()->info("Saved members data.");
    }

    public function getAll() : array 
    {
        return $this->members;
    }
}