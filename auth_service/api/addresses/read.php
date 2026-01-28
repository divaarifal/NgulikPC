<?php
// FILE: auth_service/api/addresses/read.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../utils/jwt_helper.php';

$database = new Database();
$db = $database->getConnection();

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if($user_id){
    $query = "SELECT * FROM user_addresses WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $user_id);
    $stmt->execute();
    
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["records" => $addresses]);
} else {
    echo json_encode(["message" => "User ID required."]);
}
?>
