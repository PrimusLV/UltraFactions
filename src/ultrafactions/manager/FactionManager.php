<?php
namespace ultrafactions\manager;

use pocketmine\utils\Config;
use ultrafactions\faction\Faction;
use ultrafactions\faction\FactionBuilder;
use ultrafactions\UltraFactions;

class FactionManager extends Manager
{

    /** @var Faction[] $factions */
    private $factions = [];

    public function __construct(UltraFactions $plugin)
    {
    }

    public function init() : bool
    {

        $factions = (new Config($this->getPlugin()->getDataFolder() . "factions.yml", Config::YAML))->get('factions');
        if (!empty($factions)){
            $this->loadFactions($factions);
            $this->getPlugin()->getLogger()->info("Loaded factions.");
        } else {
            $this->getPlugin()->getLogger()->info("No factions to load.");
        }

        return parent::init();
    }

    /**
     * @param array $factions
     * @return null|Faction
     */
    private function loadFactions(array $factions)
    {
        foreach ($factions as $i => $name) {
            $faction = $this->getPlugin()->getDataProvider()->getFactionData($name);
            if(empty($faction)){
                return;
            }
            if($this->loadFaction($name, $faction) instanceof Faction){
                $this->getPlugin()->getLogger()->debug("#$i: Loaded faction '$name'");
            }
        }
    }

    /**
     * @param $name
     * @param array $faction
     * @return null|Faction
     */
    private function loadFaction($name, array $faction)
    {
        try {
            $b = new FactionBuilder();
            $b->setName($name)
                ->fromArray($faction);
            $f = $b->build();
            if ($f instanceof Faction) echo "Faction " . $f->getDisplayName() . " loaded";
        } catch (\Exception $e) {
            $this->getPlugin()->getLogger()->warning("Following error occurred while loading faction: ".$e->getMessage());
            return null;
        }
        $this->factions[strtolower($f->getName())] = $f;
        return $f;
    }

    /**
     * @param $faction
     * @return null|Faction
     */
    public function getFactionByName($faction)
    {
        foreach ($this->factions as $f) {
            if ($f->getName() == strtolower($faction)) return $f;
        }
        return null;
    }

    /**
     * @param Faction $faction
     */
    public function addFaction(Faction $faction)
    {
        $this->factions[strtolower($faction->getName())] = $faction;
    }

    /**
     * @param string $name
     * @param array $data
     * @return null|Faction
     */
    public function createFaction($name, array $data)
    {
        return $this->loadFaction($name, $data);
    }

    /**
     * @param Faction $faction
     */
    public function deleteFaction(Faction $faction)
    {
    }

    /**
     * @param Faction $faction
     */
    public function saveFaction(Faction $faction)
    {
        $faction->save(); // What an useless function, isn't it? xD
    }

    public function getAll()
    {
        return $this->factions;
    }

    public function close()
    {
        $this->save();
        $this->getPlugin()->getLogger()->debug("Closed FactionManager.");
    }

    /**
     * Saves all loaded factions into faction.yml
     * And each faction class
     */
    public function save()
    {
        $factions = [];
        foreach ($this->factions as $name => $f) {
            $f->save();
            $this->getPlugin()->getLogger()->debug("Saved faction's '{$f->getName()}' data.");
            $factions[] = $name;
        }
        @unlink($this->getPlugin()->getDataFolder() . "factions.yml");
        new Config($this->getPlugin()->getDataFolder() . "factions.yml", Config::YAML, ['factions' => $factions]);
        $this->getPlugin()->getLogger()->info("Saved factions data.");
    }

}