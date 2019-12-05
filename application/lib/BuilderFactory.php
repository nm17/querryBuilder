<?php

namespace application\lib;

use Exception;

class BuilderFactory
{
    protected $driver;
    public $mysql = 'mysql';
    public $pg = 'pgsql';

    public function __construct() {
        $config = require 'application/config/db.php';
        $driver = strtolower($config['driver']);
        switch ($driver) {
            case $this->mysql:
                $this->driver = $this->mysql;
                break;
            case $this->pg:
                $this->driver = $this->pg;
                break;
            default:
                new Exception('Неизвестный тип подключения');
        }
    }
}