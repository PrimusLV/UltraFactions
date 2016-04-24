<?php
namespace ultrafactions;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use ultrafactions\data\DataProvider;
use ultrafactions\data\DefaultDataProvider;
use ultrafactions\data\MySQLDataProvider;
use ultrafactions\data\PocketCoreDataProvider;
use ultrafactions\data\SQLite3DataProvider;
use ultrafactions\handler\FactionEventListener;
use ultrafactions\handler\PlayerEventListener;
use ultrafactions\manager\FactionManager;

class UltraFactions extends PluginBase
{

    /** @var FactionManager $factionManager */
    protected $factionManager;

    /** @var DataProvider */
    private $data = null;

    /**
     * Holds loaded factions
     *
     * @var Faction[] $factions
     */
    private $factions = [];
    /**
     * Holds loaded Members
     *
     * @var Member[] $members
     */
    private $members = [];

    public static function positionFromString($string)
    {
        $d = explode(":", $string);
        $level = Server::getInstance()->getLevelByName($d[3]);
        return new Position($d[0], $d[1], $d[2], $level);
    }

    public function onLoad()
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();

        Member::setPlugin($this);
        Faction::setPlugin($this);
    }

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
            $this->getLogger()->critical("Following error occured: " . $e->getMessage());
            return; // Don't execute any furthermore code or it may cause other errors
        }

        // Load managers
        $this->factionManager = new FactionManager($this);

        // Lets register some event listeners below
        $pm->registerEvents(new PlayerEventListener($this), $this);
        $pm->registerEvents(new FactionEventListener($this), $this);

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
        $this->getLogger()->info("Saving Members...");
        foreach ($this->members as $member) {
            $member->save();
            $this->getLogger()->debug("Saved member '" . $member->getName() . "'");
        }
        $this->getLogger()->info("Members saved.");
        $this->getLogger()->info("Saving Factions...");
        foreach ($this->factions as $faction) {
            $faction->save();
            $this->getLogger()->debug('Saved faction \'' . $faction->getName() . '\'');
        }
        $this->getLogger()->info("Factions saved.");
    }

    public function getDataProvider() : DataProvider
    {
        return $this->data;
    }


    public function isInFaction(Player $player)
    {
        return $this->getPlayerFaction($player) instanceof Faction;
    }

    /**
     *
     *
     * @param Player $player
     * @return Faction|null
     */
    public function getPlayerFaction(Player $player)
    {
        if (($m = $this->getMember($player)) != null) {
            return $m->getFaction();
        }
        return null;
    }

    /**
     * @param Player $player
     * @return null|\ultrafactions\Member
     */
    public function getMember(Player $player)
    {
        if (isset($this->members[strtolower($player->getName())])) {
            return $this->members[strtolower($player->getName())];
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
        if ($this->getMember($player) === null) { // Let's make sure that player hasn't registered before
            $memberD = $this->data->getMemberData(DataProvider::playerName($player));
            $this->members[strtolower($player->getName())] = new Member($player, $memberD);
            return true;
        }
        return false;
    }

    /**
     * @return FactionManager
     */
    public function getFactionManager()
    {
        return $this->factionManager;
    }


}