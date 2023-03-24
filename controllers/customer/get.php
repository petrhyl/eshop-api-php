<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

require_once '../../config/bootstrap.php';

set_error_handler("ErrorExit::handleError");
set_exception_handler("ErrorExit::handleException");

// * * * validate params * * *

if (!isset($_GET['firstname']) || !isset($_GET['lastname']) || !isset($_GET['email'])) {
    throw new RangeException("Parameter is missing!", 400);
}

$frstnm = strip_tags($_GET['firstname']);
$lstnm = strip_tags($_GET['lastname']);
$mail = strip_tags($_GET['email']);

$validate = new InputValidation();

$validate->ValidateCustomer($frstnm, $lstnm, $mail);

// * * * set database connection * * *

$db = new Database(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);

$conn = $db->getConnection();

$customer = new Customer($conn);

// * * * execute request * * *

$id = $customer->findCustomerIdByNameAndEmail($frstnm, $lstnm, $mail);

if ($id === false) {
    throw new UnexpectedValueException('Internal error. Something went wrong.', 500);
}

http_response_code(200);
echo json_encode(["id_customer" => $id]);
