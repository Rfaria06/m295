<?php

namespace ext;

use mysqli;

class db_plesk
{
    private string $DB_HOST = 'plesk02.axanet.ch';
    private string $DB_USER = 'raul_m295';
    private string $DB_PASSWORD = '2!h2txX16';
    private string $DB_NAME = 'raul_m295';
    private mysqli $conn;

    public function __construct() {
        $this->conn = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD, $this->DB_NAME);

        $this->conn->set_charset('utf8');

        if ($this->conn->connect_error) {
            die("Connection failed: ". $this->conn->connect_error);
        }
    }

    public function getConnection(): mysqli
    {
        return $this->conn;
    }
}