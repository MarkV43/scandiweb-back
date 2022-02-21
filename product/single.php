<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/Product.php';

$db = new Database();
$product = new Product($db->getConnection());

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $product->sku = $_GET['sku'];
        $row = $product->get();
        if ($row['name'] != null) {
            $product_arr = array(
                "sku" => $row['sku'],
                "name" => $row['name'],
                "price" => $row['price'],
                "type" => $row['type'],
                "specific" => $row['specific']
            );

            http_response_code(200);
            echo json_encode($product_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Product does not exist."));
        }
        break;
    case 'POST':
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
    case 'PUT':
        parse_str(file_get_contents("php://input"), $_PUT);
        if (!empty($_GET['sku']) && !empty($_PUT['name']) && !empty($_PUT['price']) && !empty($_PUT['type']) && !empty($_PUT['specific'])) {
            $product->sku = $_GET['sku'];
            $product->name = $_PUT['name'];
            $product->price = $_PUT['price'];
            $product->type = $_PUT['type'];
            $product->specific = $_PUT['specific'];
            if ($product->update()) {
                http_response_code(201);
                echo json_encode(array("message" => "Product was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update product."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update product. Data is incomplete."));
        }
        break;
    case 'DELETE':
        $product->sku = $_GET['sku'];
        if ($product->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Product was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete product."));
        }
        break;
}