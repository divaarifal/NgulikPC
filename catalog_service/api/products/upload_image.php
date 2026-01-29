<?php
// FILE: catalog_service/api/products/upload_image.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

if(isset($_FILES['product_image'])) {
    $project_root = dirname(dirname(dirname(__DIR__))); // c:\xampp\htdocs\NgulikPC
    $target_dir = $project_root . "/frontend_main/assets/uploads/products/"; 
    if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_ext = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
    $file_name = "prod_" . time() . "_" . rand(1000,9999) . "." . $file_ext;
    $target_file = $target_dir . $file_name;
    
    if(move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
        $public_path = "assets/uploads/products/" . $file_name;
        echo json_encode([
            "message" => "Upload successful.",
            "path" => $public_path
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Upload failed."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "No file uploaded."]);
}
?>
