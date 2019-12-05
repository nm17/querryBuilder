<?php

namespace application\lib;

use PDO;
use PDOException;

class Connect
{
    private $db;

    public function __construct() {
        $config = require 'application/config/db.php';
        try {
            $this->db = new PDO($config['driver'].':host='.$config['host'].';dbname='.$config['name'].'', $config['user'], $config['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit('Подключение не удалось: '. $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            //Биндим
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }
        $stmt->execute();
        return $stmt;
    }
}