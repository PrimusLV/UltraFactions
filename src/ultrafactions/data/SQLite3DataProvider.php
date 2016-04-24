<?php
namespace ultrafactions\data;

use ultrafactions\UltraFactions;

class SQLite3DataProvider extends DataProvider
{

    public function __construct(UltraFactions $plugin)
    {
        parent::__construct($plugin);
    }

    public function getFactionData($name) : array
    {

    }

    public function getMemberData($name) : array
    {

    }

    public function setFactionData($name, array $data, $append = true) : array
    {

    }

    public function setMemberData($name, array $data, $append = true) : array
    {

    }

    protected function init() : bool
    {
        return true;
    }

}