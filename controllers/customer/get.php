<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

require_once '../../config/bootstrap.php';

$err= new ErrorExit();

// * * * validate params * * *

if (!isset($_GET['firstname']) || !isset($_GET['lastname']) || !isset($_GET['email'])) {
    $err->exitEcho(400, "Parameter is missing!");
}

$frstnm = strip_tags($_GET['firstname']);
$lstnm = strip_tags($_GET['lastname']);
$mail = strip_tags($_GET['email']);

$validate = new InputValidation($err);

$validate->ValidateCustomer($frstnm, $lstnm, $mail);

// * * * set database connection * * *

$db = new Database(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);

try {
    $conn = $db->getConnection();
} catch (PDOException $ex) {
    $err->exitEcho(500, 'Cannot connect to a database.');
}

$customer = new Customer($conn);

// * * * execute request * * *

$id = $customer->findCustomerIdByNameAndEmail($frstnm, $lstnm, $mail);

if ($id === false) {
    $err->exitEcho(500, 'Internal error. Something went wrong.');
}

http_response_code(200);
echo json_encode(["id_customer" => $id]);