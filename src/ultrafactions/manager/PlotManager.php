<?php
namespace ultrafactions\manager;

use pocketmine\level\Position;
use ultrafactions\faction\Faction;

class PlotManager extends Manager
{

    /**
     * Plots are saved like
     * Faction -> World -> plot (x:y)
     *
     * @var array
     */
    protected $plots = [];
    protected $protectedWorlds = [];

    public function __construct()
    {
    }

    public function init() : bool
    {
        $plots = $this->getPlugin()->getDataProvider()->get('plots');
        if (!empty($plots)) {
            # Load
            $this->plots = $plots;
        }
        // DEBUG
        $out = ['factionCount' => 0, 'worldCount' => 0, 'loadedPlotCount' => 0];
        $out['factionCount'] = count($plots);
        foreach ($plots as $faction => $world) {
            $out['worldCount'] += count($faction);
            $out['loadedPlotCount'] += count($world);
        }

        $this->getPlugin()->getLogger()->debug("Loaded: " . $out['loadedPlotCount'] . " plots in " . $out['worldCount'] . " worlds, for " . $out['factionCount'] . " factions");

        return parent::init();
    }

    public function close()
    {
        $this->save();
        $this->getPlugin()->getLogger()->debug("Closed PlotManager.");
    }

    private function save()
    {
        $this->getPlugin()->getDataProvider()->save('plots', $this->plots);
        $this->getPlugin()->getLogger()->debug('Saved plots');
    }

    // Plots

    /**
     * $x and $z are real coordinates not chunk's
     * @param Faction $faction
     * @param Position $pos
     * @param bool $force
     * @return bool
     */
    public function claimPlot(Faction $faction, Position $pos, $force = false) : bool
    {
        if ($this->getPlotOwner($pos) != null and !$force) return false;
        if ($this->isAreaProtected($pos) and !$force) return false;
        $this->plots[$faction->getName()][$pos->getLevel()->getName()][] = self::hashPlot($pos);
        return $this->getPlotOwner($pos) === $faction;
        //return true; # TODO
    }

    /**
     * @param Position $pos
     * @return null|\ultrafactions\Faction
     */
    public function getPlotOwner(Position $pos)
    {
        $name = $pos->getLevel()->getName();
        $hash = self::hashPlot($pos);
        foreach ($this->plots as $faction => $world) {
            foreach ($world as $worldName => $plots) {
                foreach ($plots as $plot) {
                    if ($worldName == $name and $hash == $plot) {
                        if ($f = $this->getPlugin()->getFactionManager()->getFactionByName($faction)) {
                            return $f;
                        } else {
                            # Remove faction plots if faction is invalid?
                            break;
                        }
                    }
                }
            }
        }
        return null;
    }

    public static function hashPlot(Position $pos) : string
    {
        return ($pos->x >> 4) . ":" . ($pos->z >> 4);
    }

    /**
     * @param Position $pos
     * @return bool
     */
    public function isAreaProtected(Position $pos) : bool
    {
        if (array_key_exists($pos->getLevel()->getName(), $this->protectedWorlds)) return true;
        # TODO
        return false;
    }

    /**
     * @param Position $pos
     * @return array
     */
    public function unclaimPlot(Position $pos) : array
    {
        if (($f = $this->getPlotOwner($pos)) == null) return false;
        unset($this->plots[$f->getName()][$pos->getLevel()->getName()][self::hashPlot($pos)]);
        return true;
    }

    /**
     * @param Faction $faction
     * @return array
     */
    public function getPlotsByFaction(Faction $faction) : array
    {
        if (isset($this->plots[$faction->getName()])) {
            return $this->plots[$faction->getName()];
        }
        return [];
    }


}