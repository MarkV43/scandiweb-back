<?php

include_once '../config/Table.php';

class Product extends Table {
    public function __construct($conn) {
        $params = ["name", "price", "type", "specific"];
        parent::__construct(
            $conn,
            "products",
            "sku",
            $params
        );

        parse_str(file_get_contents("php://input"), $data);

        $this->sku = $_GET["sku"] ?? null;
        foreach ($params as $param) {
            $this->$param = $this->sanitize($data[$param] ?? null);
        }

        $this->respond();
    }
}