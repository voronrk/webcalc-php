<?php

namespace Data;

define('DB_HOST', '127.0.0.1:3306');
define('DB_NAME', 'webcalc');
define('DB_USER', 'root');
define('DB_PASS', '');

require_once('Interfaces/Database.php');

use Interfaces\DatabaseInterface;

class Database implements DatabaseInterface
{

    function db_query($sql=''): array {
        if (empty($sql)) return false;
        return $this->db->query($sql);
    }

    function db_exec($sql=''): array {
        if (empty($sql)) return false;
        return $this->db->exec($sql);
    }

    public function __construct()
    {
        try {
            $this->db = new PDO("mysql:host=" . DB_HOST . "; dbname=" . DB_NAME . "; charset=utf8", DB_USER, DB_PASS, [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch(PDOException $e) {
            die($e-> getMessage());
        };
    }
}

