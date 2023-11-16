<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require '../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// get database connection
include_once '../config/database.php';
include_once '../objects/product.php';
include_once '../objects/user.php';

$database = new Database();
$db       = $database->getConnection();

$product = new Product($db);
$user_object = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // get posted data
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->jwt)) {
        try {
            $secret_key   = 'ashique-secret-key';
            $decoded_data = JWT::decode($data->jwt, new Key($secret_key, 'HS256'));

            // make sure data is not empty
            if (!empty($data->name) && !empty($data->price) && !empty($data->description) && !empty($data->category_id))
            {
                // set product property values
                $product->name        = $data->name;
                $product->price       = $data->price;
                $product->description = $data->description;
                $product->category_id = $data->category_id;
                $product->created     = date('Y-m-d H:i:s');

                // create the product
                if ($product->create()) {
                    http_response_code(201);
                    echo json_encode(array("message" => "Product was created.", "user_data" => $decoded_data));
                }
                else {
                    http_response_code(503);
                    echo json_encode(array("message" => "Unable to create product."));
                }
            }
            else {
                http_response_code(400);
                echo json_encode(array("message" => "Unable to create product. Data is incomplete."));
            }
        } 
        catch (\Throwable $th) {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create product or authentication issue happend."));
        }
    }
    else {
        http_response_code(401);
        echo json_encode(array("message" => "Unauthorized."));
    }
}
else {
    http_response_code(405);
    echo json_encode(array("message" => "Wrong method."));
}