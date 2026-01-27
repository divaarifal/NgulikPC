<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM orders ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();

$orders_arr = array();
$orders_arr["records"] = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    // Fetch items for each order
    $queryItems = "SELECT * FROM order_items WHERE order_id = ?";
    $stmtItems = $db->prepare($queryItems);
    $stmtItems->bindParam(1, $id);
    $stmtItems->execute();
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    $order_item = array(
        "id" => $id,
        "user_id" => $user_id,
        "status" => $status,
        "total_price" => $total_price,
        "shipping_address" => $shipping_address,
        "created_at" => $created_at,
        "items" => $items
    );
    array_push($orders_arr["records"], $order_item);
}

http_response_code(200);
echo json_encode($orders_arr);
?>
