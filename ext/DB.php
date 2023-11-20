<?php

namespace ext;

use mysqli;

class DB
{
    private string $DB_HOST = 'schule.winnert1.dbs.hostpoint.internal';
    private string $DB_USER = 'winnert1_schule';
    private string $DB_PASSWORD = 'FEA9PNz3p+tu+8!?MPrP';
    private string $DB_NAME = 'winnert1_m295raul';
    private mysqli $conn;

    public function __construct() {
        $this->conn = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD, $this->DB_NAME);

        $this->conn->set_charset('utf8');

        if ($this->conn->connect_error) {
            die("Connection failed: ". $this->conn->connect_error);
        }
    }

    public function getConn(): mysqli
    {
        return $this->conn;
    }
}