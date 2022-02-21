<?php

class Product {
    private $conn;
    private $table_name = "products";

    public $sku;
    public $name;
    public $price;
    public $type;
    public $specific;

    public function __construct($db) {
        $this->conn = $db;
    }

    function get() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE sku=:sku LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $this->sku = htmlspecialchars(strip_tags($this->sku));
        $stmt->bindParam(":sku", $this->sku);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function read() {
        $query = "SELECT * FROM " . $this->table_name . ";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET sku=:sku, name=:name, price=:price, type=:type, `specific`=:specific;";

        $stmt = $this->conn->prepare($query);

        $this->sku      = htmlspecialchars(strip_tags($this->sku));
        $this->name     = htmlspecialchars(strip_tags($this->name));
        $this->price    = htmlspecialchars(strip_tags($this->price));
        $this->type     = htmlspecialchars(strip_tags($this->type));
        $this->specific = htmlspecialchars(strip_tags($this->specific));

        $stmt->bindParam(":sku", $this->sku);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":specific", $this->specific);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, price=:price, type=:type, `specific`=:specific WHERE sku=:sku;";
        $stmt = $this->conn->prepare($query);

        $this->sku      = htmlspecialchars(strip_tags($this->sku));
        $this->name     = htmlspecialchars(strip_tags($this->name));
        $this->price    = htmlspecialchars(strip_tags($this->price));
        $this->type     = htmlspecialchars(strip_tags($this->type));
        $this->specific = htmlspecialchars(strip_tags($this->specific));

        $stmt->bindParam(":sku", $this->sku);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":specific", $this->specific);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE sku=:sku;";

        $stmt = $this->conn->prepare($query);

        $this->sku = htmlspecialchars(strip_tags($this->sku));

        $stmt->bindParam(":sku", $this->sku);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}