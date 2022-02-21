<?php

/*
 * This should not be needed as the method delete is already implemented in the file `single.php`,
 * but because of the way 000webhost works, I cannot use the HTTP method DELETE unless I have
 * a paid membership. That's why this file was created.
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/Product.php';

$db = new Database();
$product = new Product($db->getConnection());

if (!empty($_GET['sku'])) {
    $product->sku = $_GET['sku'];
    if ($product->delete()) {
        http_response_code(200);
        echo json_encode(array("message" => "Product was deleted."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to delete product."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to delete product. Data is incomplete."));
}