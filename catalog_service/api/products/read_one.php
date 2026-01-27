<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$slug = isset($_GET['slug']) ? $_GET['slug'] : die();

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.slug = ?
          LIMIT 0,1";

$stmt = $db->prepare($query);
$stmt->bindParam(1, $slug);
$stmt->execute();

if($stmt->rowCount() > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);
    $product_item = array(
        "id" => $id,
        "name" => $name,
        "slug" => $slug,
        "price" => $price,
        "description" => html_entity_decode($description),
        "brand" => $brand,
        "category_name" => $category_name,
        "images" => json_decode($images),
        "specs" => json_decode($specs)
    );
    http_response_code(200);
    echo json_encode($product_item);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Product not found."));
}
?>
