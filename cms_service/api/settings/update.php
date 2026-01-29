<?php
// FILE: cms_service/api/settings/update.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if(!empty($data)){
    foreach($data as $key => $value) {
        $query = "INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :val) 
                  ON DUPLICATE KEY UPDATE setting_value = :val";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":val", $value);
        $stmt->bindParam(":key", $key);
        $stmt->execute();
    }
    echo json_encode(array("message" => "Settings updated."));
} else {
    echo json_encode(array("message" => "No data provided."));
}
?>
