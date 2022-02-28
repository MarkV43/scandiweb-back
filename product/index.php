<?php

include_once '../config/Database.php';
include_once '../objects/Product.php';

// Enable possibility of overriding method in case server cannot handle it
if (isset($_GET['method'])) {
    $_SERVER['REQUEST_METHOD'] = $_GET['method'];
}

$db = new Database();
new Product($db->getConnection());