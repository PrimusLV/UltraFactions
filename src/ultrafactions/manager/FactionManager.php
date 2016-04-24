<?php
namespace ultrafactions\manager;

use pocketmine\utils\Config;
use ultrafactions\Faction;
use ultrafactions\UltraFactions;

class FactionManager
{

    /** @var Faction[] $factions */
    private $factions = [];

    public function __construct(UltraFactions $plugin)
    {
        $this->plugin = $plugin;

        $factions = (new Config($plugin->getDataFolder() . "factions.yml", Config::YAML))->getAll();
        if (!empty($factions)) $this->loadFactions($factions);
        else $plugin->getLogger()->info("No factions to load.");
    }

    /**
     * @param array $factions
     * @return bool|null|Faction
     */
    private function loadFactions(array $factions)
    {
        foreach ($factions as $i => $name) {
            $faction = $this->getPlugin()->getDataProvider()->getFactionData($name);
            if (Faction::isValidFactionData($faction)) {
                return $this->loadFaction($name, $faction);
            } else {
                $this->plugin->getLogger()->warning("Failed to load plugin ");
            }
        }
        return false;
    }

    public function getPlugin() : UltraFactions
    {
        return $this->plugin;
    }

    /**
     * @param $name
     * @param array $faction
     * @return bool|null|Faction
     */
    private function loadFaction($name, array $faction)
    {
        if (Faction::isValidFactionData($faction)) {
            $this->factions[strtolower($name)] = new Faction($name, $faction);
            return $this->getFaction($name);
        }
        return false;
    }

    /**
     * @param $name
     * @return null|Faction
     */
    public function getFaction($name)
    {
        if (isset($this->factions[strtolower($name)])) {
            return $this->factions[strtolower($name)];
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
    }

    public function close()
    {
        $this->save();
        $this->plugin->getLogger()->info("Closed FactionManager.");
    }

    /**
     * Saves all loaded factions into faction.yml
     * And each faction class
     */
    public function save()
    {
        $l = $this->getPlugin()->getLogger();
        $l->info("Saving factions...");
        $factions = [];
        foreach ($this->factions as $name => $f) {
            $factions[] = $name;
        }
        @unlink($this->getPlugin()->getDataFolder() . "factions.yml");
        new Config($this->getPlugin()->getDataFolder() . "factions.yml", Config::YAML, $factions);
    }
}