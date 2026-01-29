<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)){
    $query = "UPDATE products SET 
        name=:name, 
        price=:price, 
        description=:description, 
        category_id=:category_id, 
        brand=:brand";

    // Conditionally update images only if provided
    if(isset($data->images)) {
        $query .= ", images=:images";
    }

    $query .= " WHERE id=:id";

    // Note: Not updating slug/specs for simplicity in V1.1 unless provided
    
    $stmt = $db->prepare($query);

    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":price", $data->price);
    $stmt->bindParam(":description", $data->description);
    $stmt->bindParam(":category_id", $data->category_id);
    $stmt->bindParam(":brand", $data->brand);
    $stmt->bindParam(":id", $data->id);

    if(isset($data->images)){
        $images = json_encode($data->images);
        $stmt->bindParam(":images", $images);
    }

    if($stmt->execute()){
        http_response_code(200);
        echo json_encode(array("message" => "Product updated."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update product."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data. ID required."));
}
?>
