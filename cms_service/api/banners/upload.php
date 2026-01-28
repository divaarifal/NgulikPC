<?php
// FILE: cms_service/api/banners/upload.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

if(isset($_FILES['banner_image'])) {
    $target_dir = "../../../frontend_main/assets/uploads/banners/"; // Check correct relative path
    // cms_service/api/banners/ -> ../../../ -> NgulikPC/ -> frontend_main/...
    if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_ext = strtolower(pathinfo($_FILES["banner_image"]["name"], PATHINFO_EXTENSION));
    $file_name = "banner_" . time() . "." . $file_ext;
    $target_file = $target_dir . $file_name;
    
    if(move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file)) {
        // Return the path relative to frontend_main
        $public_path = "assets/uploads/banners/" . $file_name;
        
        // Update DB? Or just return path?
        // Let's just return path, and let the CMS settings update call handle the saving string.
        // OR we can 'Create Banner' here if it's a Banner entity. 
        // For 'CMS Settings' (Main Page Banner), it's a Single Value.
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
