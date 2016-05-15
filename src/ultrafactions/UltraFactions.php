<?php
namespace ultrafactions;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as Text;
use ultrafactions\command\FactionCommand;
use ultrafactions\data\DataProvider;
use ultrafactions\data\DefaultDataProvider;
use ultrafactions\data\MySQLDataProvider;
use ultrafactions\data\PocketCoreDataProvider;
use ultrafactions\data\SQLite3DataProvider;
use ultrafactions\economy\Economy;
use ultrafactions\handler\FactionEventListener;
use ultrafactions\handler\PlayerEventListener;
use ultrafactions\manager\FactionManager;
use ultrafactions\manager\Manager;
use ultrafactions\manager\MemberManager;
use ultrafactions\manager\PlotManager;

// Command

class UltraFactions extends PluginBase
{

    /** @var FactionManager $factionManager */
    protected $factionManager;
    /** @var MemberManager $memberManager */
    protected $memberManager;
    /** @var  PlotManager $plotManager */
    protected $plotManager;
    /** @var Economy $economy */
    protected $economy;

    /** @var array $prices */
    protected $prices;

    /** @var DataProvider */
    private $data = null;

    public function onLoad()
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();

        Manager::setPlugin($this);
        Member::setPlugin($this);
        Faction::setPlugin($this);
    }

    /**
     * Normal plugin init :P
     */
    public function onEnable()
    {
        $pm = $this->getServer()->getPluginManager();

        try {
            $this->loadDataProvider();
            if ($this->data->isValid() === false) {
                throw new \Exception("Failed to init data provider");
            }
        } catch (\Exception $e) {
            $this->getServer()->getPluginManager()->disablePlugin($this);
            $this->getLogger()->critical("Following error occurred while initializing data provider: " . $e->getMessage());
            return; // Don't execute any furthermore code or it may cause other errors
        }
        $this->economy = new Economy($this, $this->getConfig()->get('economy')['prefer']);
        if(!$this->economy->isLoaded()){
            $this->getLogger()->warning("No Economy plugin loaded!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        // Load prices
        $prices = $this->getConfig()->get('prices');
        $this->prices = $prices;
        $this->getLogger()->info("Prices loaded.");

        // Load managers
        $this->getLogger()->debug("Loading managers...");
        $this->factionManager = new FactionManager($this);
        $this->memberManager = new MemberManager($this);
        $this->plotManager = new PlotManager($this);
        $this->getLogger()->debug("Managers loaded.");

        // Lets register some event listeners below
        $pm->registerEvents(new PlayerEventListener($this), $this);
        $pm->registerEvents(new FactionEventListener($this), $this);

        // Function name sucks, It will be register only one command
        $this->registerCommands();
        $this->getLogger()->debug("Commands registered.");

        $this->getLogger()->info(Text::GREEN."Plugin loaded.");
    }

    private function loadDataProvider()
    {
        $providerType = $this->getConfig()->get('data');
        if (!$providerType) {
            throw new \Exception('Can not receive \'data\' from config', 0);
        }
        if(!isset($providerType['provider'])){
            throw new \Exception('Unset \'provider\' under \'data\' key');
        }
        switch (strtolower($providerType['provider'])) {
            case 'yaml': # YAML is default data provider
            default:
                $this->data = new DefaultDataProvider($this);
                break;
            case 'mysql':
                $this->data = new MySQLDataProvider($this);
                break;
            case 'sql3':
            case 'sqlite3':
                $this->data = new SQLite3DataProvider($this);
                break;
            case 'pocketcore':
            case 'pc':
                $this->data = new PocketCoreDataProvider($this);
                break;
        }
        $this->getLogger()->info("Data provider: " . $this->data->getType());
        return true;
    }

    private function registerCommands()
    {
        $map = $this->getServer()->getCommandMap();
        $map->register("UltraFactions", new FactionCommand($this));
    }

    public function onDisable()
    {
        # Save all factions
        if ($this->factionManager != null) $this->factionManager->close();
        # Save members
        if ($this->factionManager != null) $this->memberManager->close();
        # And close connections or so on data provider class
        if ($this->data != null) $this->data->close();

        $this->getLogger()->info(Text::LIGHT_PURPLE . "Plugin disabled.");
    }

    // API Functions

    public function getDataProvider() : DataProvider
    {
        return $this->data;
    }

    /**
     * @return FactionManager
     */
    public function getFactionManager() : FactionManager
    {
        return $this->factionManager;
    }

    /**
     * @return MemberManager
     */
    public function getMemberManager()
    {
        return $this->memberManager;
    }

    /**
     * @return PlotManager
     */
    public function getPlotManager()
    {
        return $this->plotManager;
    }

    /**
     * @return Economy
     */
    public function getEconomy() : Economy {
        return $this->economy;
    }

    /**
     * @param string $node
     * @return int
     */
    public function getPrice($node) : int {
        $keys = explode('.', $node);
        $i = 0;
        if(isset($this->prices[$keys[$i]])){
            $prices = $this->prices[$keys[$i]];
            while(is_array($prices)){
                $i++;
                $prices = isset($prices[$keys[$i]]) ? $prices[$keys[$i]] : 0;
            }
            return $prices;
        } else {
            return 0;
        }
    }


}