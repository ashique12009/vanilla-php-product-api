<?php 
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');

require '../../vendor/autoload.php';
use Firebase\JWT\JWT;

include_once '../config/database.php';
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();

$user_object = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->email) && !empty($data->password)) {
        $user_object->email    = $data->email;
        $user_object->password = $data->password;

        $user_data = $user_object->check_login();

        if (password_verify($user_object->password, $user_data['password'])) {
            http_response_code(200);
            echo json_encode(
                array('message' => 'User logged in', 'code' => 200)
            );
        }
        else {
            http_response_code(401);
            echo json_encode(
                array('message' => 'Invalid email or password', 'code' => 401)
            );
        }
    }
    else {
        http_response_code(400);
        echo json_encode(
            array('message' => 'All data needed', 'code' => 400)
        );
    }
}