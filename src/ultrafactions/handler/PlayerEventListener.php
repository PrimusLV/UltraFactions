<?php
namespace ultrafactions\handler;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use ultrafactions\Member;
use ultrafactions\UltraFactions;

class PlayerEventListener implements Listener
{
    /** @var UltraFactions $plugin */
    private $plugin;

    private $chatFormat;

    public function __construct(UltraFactions $plugin)
    {
        $this->plugin = $plugin;

        if ($cf = $plugin->getConfig()->get('chat-format')) {
            $this->chatFormat = $cf;
        }
    }

    /**
     * @param PlayerPreLoginEvent $e
     * @ignoreCancelled false
     * @priority HIGHEST
     */
    public function onPlayerPreLogin(PlayerPreLoginEvent $e)
    {
        $p = $e->getPlayer();


        # Create Member class if player is in any faction
        if ($this->getPlugin()->getMemberManager()->isMember($p)) {
            echo "Player is member";
            $this->getPlugin()->getMemberManager()->registerPlayer($p);
        } else {
            echo "Player is not a member";
        }
    }

    public function getPlugin() : UltraFactions
    {
        return $this->plugin;
    }

    /**
     * @param PlayerChatEvent $e
     * @ignoreCancelled false
     * @priority HIGHEST
     */
    public function onPlayerChat(PlayerChatEvent $e)
    {
        $p = $e->getPlayer();
        if ($this->getPlugin()->isInFaction($p)) {
            $m = $this->plugin->getMember($p);
            $f = $m->getFaction(); // Well this should return Faction
            $cf = $this->chatFormat;
            /** @var Member $m */
            $cf = str_replace(["{PLAYER}", "{FACTION}", "{RANK}", "{MESSAGE}"], [$p->getDisplayName(), $f->getName(), $m->getRank(), $e->getMessage()], $cf);
            $e->setFormat($cf);
        }
    }

    /**
     * Handle damage events and cancel them in certain conditions, like players are on same faction or their factions are allies
     *
     * @param EntityDamageEvent $e
     * @ignoreCancelled true
     * @priority MONITOR
     */
    public function onDamage(EntityDamageEvent $e)
    {
        # TODO
    }
}