<?php

namespace app\models;

use PDO;
use PDOException;

abstract class Model {

    public function findAll() {
        $query = "select * from $this->table";
        return $this->query($query);
    }

    private function connect() {
        $string = "mysql:host=" . DBHOST . ";dbname=" . DBNAME;
        $con = new \PDO($string, DBUSER, DBPASS ? DBPASS : '');
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    }

    public function query($query, $data = []) {
        $con = $this->connect();
        $stm = $con->prepare($query);
        $check = $stm->execute($data);
        if ($check) {
            $result = $stm->fetchAll(\PDO::FETCH_OBJ);
            if (is_array($result) && count($result)) {
                return $result;
            }
        }
        return false;
    }

}