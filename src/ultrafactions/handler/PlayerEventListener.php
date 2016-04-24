<?php
namespace ultrafactions\handler;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\Player;
use ultrafactions\Faction;
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

        try {
            $this->plugin->registerPlayer($p);
        } catch (\Exception $e) {
            $this->plugin->getLogger()->warning("Failed to register player '" . $p->getName() . "' (" . $e->getMessage() . ")");
        }

        // Test

        if (($m = $this->plugin->getMember($p)) instanceof Member) {
            $l = $this->plugin->getLogger();
            $l->info("Member class for player " . $p->getName() . ' was created');
            if ($m->isInFaction()) {
                $l->info("He is in faction " . $m->getFaction()->getName());
            } else {
                $l->info("He is not in faction");
            }
        }
    }

    /**
     * @param PlayerChatEvent $e
     * @ignoreCancelled false
     * @priority HIGHEST
     */
    public function onPlayerChat(PlayerChatEvent $e)
    {
        $p = $e->getPlayer();
        if ($this->plugin->isInFaction($p)) {
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
        $victim = $e->getEntity();
        if ($e instanceof EntityDamageByEntityEvent) {
            $attacker = $e->getDamager();
            if ($attacker instanceof Player and $victim instanceof Player) {
                $vM = $this->plugin->getMember($attacker);
                $aM = $this->plugin->getMember($attacker);
                if ($vM->getFaction() instanceof Faction and $aM->getFaction() instanceof Faction) {
                    /** @var Faction $vF */
                    $vF = $vM->getFaction();
                    /** @var Faction $aF */
                    $aF = $aM->getFaction();
                    if ($vF === $aF) {
                        $e->setCancelled(true);
                        $attacker->sendTip("You are on the same faction");
                    } elseif ($aF->isAlly($vF)) {
                        $e->setCancelled(true);
                        $attacker->sendTip("Your faction is in allies with his");
                    }
                }
            }
        }
    }

    public function getPlugin() : UltraFactions
    {
        return $this->plugin;
    }
}