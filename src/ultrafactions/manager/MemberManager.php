<?php
/**
 * Created by PhpStorm.
 * User: primus
 * Date: 4/25/16
 * Time: 6:57 PM
 */

namespace ultrafactions\manager;

use ultrafactions\data\DataProvider;
use ultrafactions\UltraFactions;
use ultrafactions\Member;
use ultrafactions\Faction;

use pocketmine\Player;

class MemberManager
{

    /** @var UltraFactions $plugin */
    private $plugin;

    /** @var Member[] $members */
    private $members = [];

    public function __construct(UltraFactions $plugin){
        $this->plugin = $plugin;
    }

    public function getMember(Player $player) : Member {
        # TODO
        if(isset($this->members[strtolower($player->getName())])){
            return $this->members[strtolower($player->getName())];
        } else {
            return $this->registerMember($player);
        }
    }

    public function loadMember($name){
        # TODO
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
                        throw new \Exception("Member is in invalid/unloaded faction");
                    }
                } catch (\Exception $e) {
                    $this->getPlugin()->getLogger()->warning("Following error occurred while registering player: " . $e->getMessage());
                }
            }
            $m = new Member($player, $faction, $power, $stats);
            $this->members[strtolower($player->getName())] = $m;
            return $m;
    }

    public function getMembers() : array {
        return $this->members;
    }

    public function getPlugin() : UltraFactions {
        return $this->plugin;
    }

    public function save(){
        foreach($this->members as $member){
            $member->save();
            $this->getPlugin()->getLogger()->debug("Saved member's '{$member->getName()}' data");
        }
            $this->getPlugin()->getLogger()->info("Saved members data.");
    }

    public function close()
    {
        $this->save();
        $this->getPlugin()->getLogger()->debug("Closed MemberManager.");
    }
}