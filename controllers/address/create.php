<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST,PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With');

require_once '../../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    die();
}

set_error_handler("ErrorExit::handleError");
set_exception_handler("ErrorExit::handleException");

$data = (array)json_decode(file_get_contents("php://input"), true);

// * * * data validation * * *

if (count($data) < 5) {
    throw new RangeException("Missing data to save address.", 400);
}

$customer = $data['customerId'];
$street = trim(strip_tags($data['street']));
$house = trim(strip_tags($data['house']));
$town = trim(strip_tags($data['town']));
$postal = preg_replace('/\s+/', '', strip_tags($data['postal']));
$country = trim(strip_tags($data['country']));

$validate = new InputValidation();

$validate->ValidateAddress($customer, $street, $house, $town, $postal, $country);

// * * * set database * * *

$db = new Database(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);

try {
    $conn = $db->getConnection();
} catch (PDOException $ex) {
    throw new UnexpectedValueException("Internal error. Cannot connect to database.", 500);
}

// * * * create address * * *

$address = new Address($conn);

$address_arr = $address->findAddressesByCustomerId($customer);

if ($address_arr === false) {
    throw new UnexpectedValueException("Internal error. Something is wrong.", 500);
}

$address_match = false;
if (count($address_arr) > 0) {
    for ($i = 0; $i < count($address_arr); $i++) {
        if (
            $address_arr[$i]->street === $street &&
            $address_arr[$i]->house === $house &&
            $address_arr[$i]->town === $town &&
            $address_arr[$i]->postal_code === $postal &&
            $address_arr[$i]->country === $country
        ) {
            $address_match = true;
        }
    }
}

if ($address_match) {
    throw new InvalidArgumentException("Address for the customer allready exists.", 409);
}

$address->street = $street;
$address->house = $house;
$address->town = $town;
$address->postal_code = $postal;
$address->country = $country;
$address->id_customer = $customer;

$result = $address->createAddressOfCustomer();

if (!$result) {
    throw new UnexpectedValueException("Internal error. Cannot save customer's address.", 500);
}

$address_arr[0]->id_address = $result;


http_response_code(201);
echo json_encode(['addressId' => $address_arr[0]->id_address]);
