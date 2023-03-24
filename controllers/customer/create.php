<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Origin, Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    die();
}

require_once '../../config/bootstrap.php';

set_error_handler("ErrorExit::handleError");
set_exception_handler("ErrorExit::handleException");

// * * * * *
// * api example: localhost:8008/api/controllers/customer/create.php
// * * * * *
// {
//     "firstname":"Aleš",
//     "lastname":"Valenta",
//     "phone":655893733,
//     "email":"ales.valenta@gmail.com",
//     "address":{
//         "street":"Mělká",
//         "house": "701",
//         "town":"Mírovec",
//         "postal":"23435",
//         "country":"Česká republika"
//     }
// }

$data = (array)json_decode(file_get_contents("php://input"), true);

// * * * data validation * * *

if (count($data) < 5) {
    throw new RangeException("Missing data to save custom's details.", 400);
}

$frstnm = trim(strip_tags($data['firstname']));
$lstnm = trim(strip_tags($data['lastname']));
$phone = trim(strip_tags($data['phone']));
$mail = trim(strip_tags($data['email']));

if (count($data['address']) < 5) {
    throw new RangeException("Missing data to save customer's address.", 400);
}

$street = trim(strip_tags($data['address']['street']));
$house = trim(strip_tags($data['address']['house']));
$town = trim(strip_tags($data['address']['town']));
$postal = preg_replace('/\s+/', '', strip_tags($data['address']['postal']));
$country = trim(strip_tags($data['address']['country']));

$validate = new InputValidation();

$validate->ValidateCustomerWithAddress($frstnm, $lstnm, $phone, $mail, $street, $house, $town, $postal, $country);

// * * * set database * * *

$db = new Database(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);

$conn = $db->getConnection();

// * * * create customer * * *

$customer = new Customer($conn);

$id_customer = $customer->findCustomerIdByNameAndEmail($frstnm, $lstnm, $mail);

if ($id_customer === false) {
    throw new UnexpectedValueException("Internal error. Something went wrong.", 500);
}

if ($id_customer < 1) {
    $customer = $customer->findCustomerByEmail($mail);

    if (!$customer) {
        throw new UnexpectedValueException("Internal error. Something went wrong.", 500);
    }

    if ($customer->id_customer > 0) {
        throw new InvalidArgumentException("The e-mail address is allready used.", 409);
    }

    $customer->firstname = $frstnm;
    $customer->lastname = $lstnm;
    $customer->phone = $phone;
    $customer->email = $mail;

    $id_customer = $customer->createCustomer();

    if (!$id_customer) {
        throw new UnexpectedValueException("Internal error. Cannot save customer's details.", 500);
    }
}

// * * * create customer's address * * *

$address = new Address($conn);

$address_arr = $address->findAddressesByCustomerId($id_customer);

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

if (!$address_match) {
    $address->street = $street;
    $address->house = $house;
    $address->town = $town;
    $address->postal_code = $postal;
    $address->country = $country;
    $address->id_customer = $id_customer;

    $result = $address->createAddressOfCustomer();

    if (!$result) {
        throw new UnexpectedValueException("Internal error. Cannot save customer's address.", 500);
    }
}

http_response_code(201);
echo json_encode(['customerId' => $id_customer]);
