<?php
/**
 * Created by PhpStorm.
 * User: primus
 * Date: 5/15/16
 * Time: 8:30 PM
 */

namespace ultrafactions\manager;


use ultrafactions\UltraFactions;

abstract class Manager
{
    /** @var UltraFactions $plugin */
    protected static $plugin = null;

    public static function setPlugin(UltraFactions $plugin)
    {
        if (self::$plugin === null) self::$plugin = $plugin;
    }

    public abstract function close();

    /**
     * true - if manager is ready to be used, else - false
     *
     * @return bool
     */
    protected function init() : bool
    {
        if ($this->getPlugin() instanceof UltraFactions === false) return false;
        return true;
    }

    public function getPlugin() : UltraFactions
    {
        return self::$plugin;
    }

}