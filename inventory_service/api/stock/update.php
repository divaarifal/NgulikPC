<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->product_id) && isset($data->quantity)){
    // Check if exists
    $check = "SELECT product_id FROM stocks WHERE product_id = ?";
    $stmt = $db->prepare($check);
    $stmt->bindParam(1, $data->product_id);
    $stmt->execute();

    if($stmt->rowCount() > 0){
        $query = "UPDATE stocks SET quantity = :quantity WHERE product_id = :product_id";
    } else {
        $query = "INSERT INTO stocks SET quantity = :quantity, product_id = :product_id";
    }

    $stmt = $db->prepare($query);
    $stmt->bindParam(":quantity", $data->quantity);
    $stmt->bindParam(":product_id", $data->product_id);

    if($stmt->execute()){
        http_response_code(200);
        echo json_encode(array("message" => "Stock updated."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update stock."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>
