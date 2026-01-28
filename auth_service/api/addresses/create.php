<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->user_id) &&
    !empty($data->label) &&
    !empty($data->address_line)
){
    $query = "INSERT INTO user_addresses SET user_id=:uid, label=:label, recipient_name=:name, phone_number=:phone, address_line=:addr";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":uid", $data->user_id);
    $stmt->bindParam(":label", $data->label);
    $stmt->bindParam(":name", $data->recipient_name);
    $stmt->bindParam(":phone", $data->phone_number);
    $stmt->bindParam(":addr", $data->address_line);

    if($stmt->execute()){
        echo json_encode(array("message" => "Address added."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to add address."));
    }
}
?>
