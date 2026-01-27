<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : die();

$query = "SELECT quantity, reserved FROM stocks WHERE product_id = ? LIMIT 0,1";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $product_id);
$stmt->execute();

if($stmt->rowCount() > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $available = $row['quantity'] - $row['reserved'];
    
    http_response_code(200);
    echo json_encode(array(
        "product_id" => $product_id,
        "quantity" => $row['quantity'],
        "reserved" => $row['reserved'],
        "available" => $available
    ));
} else {
    // If not found, assume 0 stock
    http_response_code(404);
    echo json_encode(array("product_id" => $product_id, "available" => 0, "message" => "Stock not found."));
}
?>
