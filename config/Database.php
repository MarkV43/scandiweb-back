<?php

class Database {
    private $host = "localhost";
    private $db_name = "id18498616_scandiweb";
    private $username = "id18498616_marcelo";
    private $password = "R_w8!A|l#/|pQGL?";

    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}