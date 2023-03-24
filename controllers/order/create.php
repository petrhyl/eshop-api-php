<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Origin, Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    die();
}

require_once '../../config/bootstrap.php';

set_error_handler("ErrorExit::handleError");
set_exception_handler("ErrorExit::handleException");

// * * * * *
// * api example: localhost:8008/api/controllers/order/create.php
// * * * * *
// {
//     "customer": {
//         "id": 4
//     },
//     "products": [
//         {
//             "id": 2,
//             "quantity": 2,
//             "price": 1200
//         },
//         {
//             "id": 3,
//             "quantity": 1,
//             "price": 3390
//         }
//     ]
// }

$data = (array)json_decode(file_get_contents("php://input"), true);

// * * * data validation * * *

if (count($data) < 2) {
    throw new RangeException("Missing data to create order.", 400);
}

$customer_id = $data['customer']['id'];
$ordered_products = $data['products'];

if (count($ordered_products) < 1) {
    throw new RangeException("Missing data to save ordered products.", 400);
}

$validate = new InputValidation();

for ($i = 0; $i < count($ordered_products); $i++) {
    $validate->ValidateOrder(
        $customer_id,
        $ordered_products[$i]['id'],
        $ordered_products[$i]['price'],
        $ordered_products[$i]['quantity']
    );
}

// * * * set database * * *

$db = new Database(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);


$conn = $db->getConnection();

// * * * create order * * *

$oreder = new Order($conn);

$oreder->id_customer = $customer_id;
$oreder->order_date = new DateTime();

$order_id = $oreder->createOrder();

if ($order_id === false) {
    throw new UnexpectedValueException("Internal error. Order cannot be created.", 500);
}

// * * * create ordered products * * * 

for ($i = 0; $i < count($ordered_products); $i++) {
    $product = new OrderedProduct($conn);

    $product->ordered_price = $ordered_products[$i]['price'];
    $product->quantity = $ordered_products[$i]['quantity'];
    $product->id_product = $ordered_products[$i]['id'];
    $product->id_order = $order_id;

    $result = $product->createOrderingProduct();
    if (!$result) {
        throw new UnexpectedValueException("Internal error. Cannto save ordered products.", 500);
    }
}

http_response_code(201);
echo json_encode(['orderId' => $order_id]);
