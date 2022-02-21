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
        $stmt = $product->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $products_arr = array();
            $products_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $product_item = array(
                    "sku" => $sku,
                    "name" => $name,
                    "price" => $price,
                    "type" => $type,
                    "specific" => $specific
                );

                $products_arr["records"][] = $product_item;
            }

            http_response_code(200);

            echo json_encode($products_arr);
        } else {
            http_response_code(404);

            echo json_encode(
                array("message" => "No products found.")
            );
        }
        break;
    case 'POST':
        if (!empty($_POST['sku']) && !empty($_POST['name']) && !empty($_POST['price']) && !empty($_POST['type']) && !empty($_POST['specific'])) {
            $product->sku = $_POST['sku'];
            $product->name = $_POST['name'];
            $product->price = $_POST['price'];
            $product->type = $_POST['type'];
            $product->specific = $_POST['specific'];
            if ($product->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Product was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create product."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create product. Data is incomplete."));
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}