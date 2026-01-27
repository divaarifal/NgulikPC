<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->name) &&
    !empty($data->price) &&
    !empty($data->category_id)
){
    $query = "INSERT INTO products SET 
        name=:name, 
        slug=:slug, 
        price=:price, 
        description=:description, 
        category_id=:category_id, 
        brand=:brand, 
        images=:images, 
        specs=:specs";

    $stmt = $db->prepare($query);

    // Slug generation
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data->name)));
    
    // Bind
    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":slug", $slug);
    $stmt->bindParam(":price", $data->price);
    $stmt->bindParam(":description", $data->description);
    $stmt->bindParam(":category_id", $data->category_id);
    $stmt->bindParam(":brand", $data->brand);
    
    $images = isset($data->images) ? json_encode($data->images) : '[]';
    $stmt->bindParam(":images", $images);
    
    $specs = isset($data->specs) ? json_encode($data->specs) : '{}';
    $stmt->bindParam(":specs", $specs);

    if($stmt->execute()){
        $id = $db->lastInsertId();
        
        // Also initialize stock in Inventory Service? 
        // Ideally yes, but for microservice isolation, the Admin Frontend should call Inventory API after success here.
        // Or we use an event bus. For simplicity, we'll let Frontend handle the 2nd call.
        
        http_response_code(201);
        echo json_encode(array("message" => "Product created.", "id" => $id));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create product."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>
