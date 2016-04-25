<?php
namespace ultrafactions;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as Text;

use ultrafactions\data\DataProvider;
use ultrafactions\data\DefaultDataProvider;
use ultrafactions\data\MySQLDataProvider;
use ultrafactions\data\PocketCoreDataProvider;
use ultrafactions\data\SQLite3DataProvider;
use ultrafactions\handler\FactionEventListener;
use ultrafactions\handler\PlayerEventListener;
use ultrafactions\manager\FactionManager;
use ultrafactions\manager\MemberManager;

class UltraFactions extends PluginBase
{

    /** @var FactionManager $factionManager */
    protected $factionManager;
    /** @var MemberManager $memberManager */
    protected $memberManager;

    /** @var DataProvider */
    private $data = null;

    public function onLoad()
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();

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

        // Load managers
        $this->factionManager = new FactionManager($this);
        $this->memberManager = new MemberManager($this);

        // Lets register some event listeners below
        $pm->registerEvents(new PlayerEventListener($this), $this);
        $pm->registerEvents(new FactionEventListener($this), $this);

        $this->getLogger()->info(Text::GREEN."Plugin loaded.");
    }

    private function loadDataProvider()
    {
        $providerType = $this->getConfig()->get('data-provider');
        if (!$providerType) {
            throw new \Exception('Can not receive \'data-provider\' from config', 0);
        }
        switch (strtolower($providerType)) {
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

    // API Functions

    public function onDisable()
    {
        # Save all factions
        $this->factionManager->close();
        # Save members
        $this->memberManager->close();
        # And close connections or so on data provider class
        $this->data->close();

        $this->getLogger()->info(Text::LIGHT_PURPLE."Plugin disabled.");
    }

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
     * Shortcut for FactionManager::getFaction()
     *
     * @param $name
     * @return null|Faction
     */
    public function getFaction($name){
        return $this->factionManager->getFaction($name);
    }

    /**
     * Shortcut for MemberManager::getFaction()
     *
     * @param Player $player
     * @return Member
     */
    public function getMember(Player $player){
        return $this->memberManager->getMember($player);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isInFaction(Player $player) : bool {
        return $this->getMember($player)->getFaction() instanceof Faction === true;
    }


}