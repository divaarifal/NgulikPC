<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : die();

$query = "SELECT SUM(quantity) as total_qty, SUM(reserved) as total_res FROM stocks WHERE product_id = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $product_id);
$stmt->execute();

if($stmt->rowCount() > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // If no rows found, sum is null. Handle that.
    $qty = $row['total_qty'] ? $row['total_qty'] : 0;
    $res = $row['total_res'] ? $row['total_res'] : 0;
    
    $available = $qty - $res;
    
    http_response_code(200);
    echo json_encode(array(
        "product_id" => $product_id,
        "quantity" => $qty,
        "reserved" => $res,
        "available" => $available
    ));
} else {
    // If not found, assume 0 stock
    http_response_code(404);
    echo json_encode(array("product_id" => $product_id, "available" => 0, "message" => "Stock not found."));
}
?>
