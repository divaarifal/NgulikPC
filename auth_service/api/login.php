<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../utils/jwt_helper.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->email) && !empty($data->password)){
    $query = "SELECT id, username, password_hash, role FROM users WHERE email = ? LIMIT 0,1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $data->email);
    $stmt->execute();

    if($stmt->rowCount() > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(password_verify($data->password, $row['password_hash'])){
            $jwtHelpers = new JWT();
            $token_payload = array(
                "id" => $row['id'],
                "username" => $row['username'],
                "role" => $row['role'],
                "exp" => time() + (60*60*24) // 24 hours
            );
            $jwt = $jwtHelpers->generate($token_payload);

            http_response_code(200);
            echo json_encode(
                array(
                    "message" => "Successful login.",
                    "token" => $jwt,
                    "user" => array(
                        "id" => $row['id'],
                        "username" => $row['username'],
                        "role" => $row['role']
                    )
                )
            );
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Login failed. Wrong password."));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Login failed. User not found."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>
