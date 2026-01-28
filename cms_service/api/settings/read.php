<?php
// FILE: cms_service/api/settings/read.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM site_settings";
$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

if($num>0){
    $arr=array();
    $arr["records"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $item = array("setting_key" => $setting_key, "setting_value" => $setting_value);
        array_push($arr["records"], $item);
    }
    echo json_encode($arr);
} else {
    echo json_encode(array("message" => "No settings found."));
}
?>
