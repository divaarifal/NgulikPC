<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)){
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $data->id);

    if($stmt->execute()){
        http_response_code(200);
        echo json_encode(array("message" => "Product deleted."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to delete product."));
    }
}
?>
