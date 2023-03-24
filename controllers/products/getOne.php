<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods: GET');

require_once '../../config/bootstrap.php';

set_error_handler("ErrorExit::handleError");
set_exception_handler("ErrorExit::handleException");

// * * * * * * * *
// api example: server_dir/api/controllers/products/getOne.php?id=2
// * * * * * * * *
// {
//     "product": {
//         "id_product": 2,
//         "name": "Toy",
//         "price": 1200,
//         "pictures": [
//             {
//                 "id_picture": 4,
//                 "file_path": "https://hyl-petr.xf.cz/images/toy_1.jpg",
//                 "id_product": 2
//             },
//             {
//                 "id_picture": 5,
//                 "file_path": "https://hyl-petr.xf.cz/images/toy_2.jpg",
//                 "id_product": 2
//             }
//         ],
//         "description": "Sit amet consectetur adipisicing elit. 
//         Ab fugiat accusantium in reiciendis dolor corporis voluptate beatae, illum aliquam, 
//         cumque asperiores quo aut ipsa possimus soluta culpa molestias magni quidem."
//     }
// }


// * * * validate params * * *

if (!isset($_GET['id'])) {
    throw new RangeException("Parameter id is missing!", 400);
}

$id_str = strip_tags($_GET['id']);
$id = filter_var($id_str, FILTER_VALIDATE_INT);

if (!$id) {
    throw new DomainException("Invalid format of id.", 422);
}


// * * * set database connection * * *

$db = new Database(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);

$conn = $db->getConnection();


$product = new Product($conn);

$data['product'] = $product->getProductById($id);

if ($data['product'] === false) {
    throw new UnexpectedValueException("No data. Something was wrong. Please contact help desk.", 500);
}

echo json_encode($data);
