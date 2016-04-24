<?php
namespace ultrafactions\handler;

use pocketmine\event\Listener;
use ultrafactions\UltraFactions;

class FactionEventListener implements Listener
{

    /** @var UltraFactions $plugin */
    private $plugin;

    public function __construct(UltraFactions $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin() : UltraFactions
    {
        return $this->plugin;
    }
}