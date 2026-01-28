<?php
// FILE: auth_service/api/user/upload_avatar.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

if(isset($_FILES['avatar_image']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $target_dir = "../../../frontend_main/assets/uploads/avatars/"; 
    if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_ext = strtolower(pathinfo($_FILES["avatar_image"]["name"], PATHINFO_EXTENSION));
    $file_name = "user_" . $user_id . "_" . time() . "." . $file_ext;
    $target_file = $target_dir . $file_name;
    
    if(move_uploaded_file($_FILES["avatar_image"]["tmp_name"], $target_file)) {
        $public_path = "assets/uploads/avatars/" . $file_name;
        
        // Update DB directly here for ease
        $query = "UPDATE users SET avatar = :avatar WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":avatar", $public_path);
        $stmt->bindParam(":id", $user_id);
        
        if($stmt->execute()) {
             echo json_encode([
                "message" => "Upload successful.",
                "path" => $public_path
            ]);
        } else {
             http_response_code(500);
             echo json_encode(["message" => "Database update failed."]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Upload failed."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "No file or User ID."]);
}
?>
