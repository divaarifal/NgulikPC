<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->username) &&
    !empty($data->email) &&
    !empty($data->password)
){
    // Check if user exists
    $check_query = "SELECT id FROM users WHERE email = ? LIMIT 0,1";
    $stmt = $db->prepare($check_query);
    $stmt->bindParam(1, $data->email);
    $stmt->execute();
    
    if($stmt->rowCount() > 0){
        http_response_code(400);
        echo json_encode(array("message" => "User already exists."));
        exit;
    }

    $query = "INSERT INTO users SET username=:username, email=:email, password_hash=:password, role=:role";
    $stmt = $db->prepare($query);

    $data->username = htmlspecialchars(strip_tags($data->username));
    $data->email = htmlspecialchars(strip_tags($data->email));
    $password_hash = password_hash($data->password, PASSWORD_BCRYPT);
    $role = isset($data->role) ? $data->role : 'user';

    $stmt->bindParam(":username", $data->username);
    $stmt->bindParam(":email", $data->email);
    $stmt->bindParam(":password", $password_hash);
    $stmt->bindParam(":role", $role);

    if($stmt->execute()){
        http_response_code(201);
        echo json_encode(array("message" => "User was created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to register user."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>
