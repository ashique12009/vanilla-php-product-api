<?php 
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

include_once '../config/database.php';
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();

$user_object = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->email) && !empty($data->password)) {
        $user_object->email = $data->email;
        $user_object->password = password_hash($data->password, PASSWORD_DEFAULT);

        if ($user_object->create_user()) {
            http_response_code(201);
            echo json_encode(
                array('message' => 'User created', 'code' => 201)
            );
        }
        else {
            http_response_code(503);
            echo json_encode(
                array('message' => 'Unable to create user', 'code' => 503)
            );
        }
    }
    else {
        http_response_code(400);
        echo json_encode(
            array('message' => 'Bad request', 'code' => 400)
        );
    }
}
else {
    http_response_code(400);
    echo json_encode(
        array('message' => 'POST request required. Bad request!', 'code' => 400)
    );
}