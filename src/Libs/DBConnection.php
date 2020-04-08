<?php

namespace App\Libs;

use Exception;
use Jajo\JSONDB;

class DBConnection {

    public static function getConnection() {

        try {
            $json_db = new JSONDB( __DIR__."/../database");
        } catch(Exception $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        return $json_db;
    }

}