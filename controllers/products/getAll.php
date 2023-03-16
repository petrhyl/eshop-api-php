<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods: GET');

require_once '../../config/bootstrap.php';

// * * * * * * * *
// api example: server_dir/api/controllers/products/getAll.php
// * * * * * * * *
// {
//     "products": [
//         {
//             "id_product": 1,
//             "name": "Teddy bear",
//             "price": 870,
//             "pictures": [
//                 {
//                     "id_picture": 1,
//                     "file_path": "https://hyl-petr.xf.cz/images/bear_1.jpg"
//                 }
//             ]
//         },
//         {
//             "id_product": 2,
//             "name": "Toy",
//             "price": 1200,
//             "pictures": [
//                 {
//                     "id_picture": 4,
//                     "file_path": "https://hyl-petr.xf.cz/images/toy_1.jpg"
//                 }
//             ]
//         }
//     ]
//}

$err = new ErrorExit();

// --- set database connection ---

$db = new Database(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);

$conn = $db->getConnection();

$product = new Product($conn);

$data['products'] = $product->getAllProducts();

if (($data['products'] === false) || (count($data['products']) === 0)) {
   
}

echo json_encode($data);
