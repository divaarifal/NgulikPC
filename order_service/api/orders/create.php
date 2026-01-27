<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// Expected data: { user_id: 1, items: [ {product_id: 1, quantity: 2, price: 50000, name: "Item"} ], total_price: 100000, shipping_address: "..." }

if(!empty($data->user_id) && !empty($data->items)){
    try {
        $db->beginTransaction();

        $query = "INSERT INTO orders SET user_id=:user_id, total_price=:total_price, shipping_address=:address, status='pending'";
        $stmt = $db->prepare($query);
        
        $address = isset($data->shipping_address) ? $data->shipping_address : "";
        
        $stmt->bindParam(":user_id", $data->user_id);
        $stmt->bindParam(":total_price", $data->total_price);
        $stmt->bindParam(":address", $address);
        
        if($stmt->execute()){
            $order_id = $db->lastInsertId();

            foreach($data->items as $item){
                $query_item = "INSERT INTO order_items SET order_id=:order_id, product_id=:product_id, product_name=:name, quantity=:quantity, price=:price";
                $stmt_item = $db->prepare($query_item);
                
                $stmt_item->bindParam(":order_id", $order_id);
                $stmt_item->bindParam(":product_id", $item->product_id);
                $stmt_item->bindParam(":name", $item->name);
                $stmt_item->bindParam(":quantity", $item->quantity);
                $stmt_item->bindParam(":price", $item->price);
                $stmt_item->execute();
            }

            $db->commit();
            http_response_code(201);
            echo json_encode(array("message" => "Order created.", "order_id" => $order_id));
        } else {
            $db->rollBack();
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create order."));
        }
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>
