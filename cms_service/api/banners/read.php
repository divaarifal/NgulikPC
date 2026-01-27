<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM banners WHERE is_active = 1 ORDER BY display_order";
$stmt = $db->prepare($query);
$stmt->execute();

$banners_arr = array();
$banners_arr["records"] = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    $banner_item = array(
        "id" => $id,
        "title" => $title,
        "image_url" => $image_url,
        "link_url" => $link_url
    );
    array_push($banners_arr["records"], $banner_item);
}

http_response_code(200);
echo json_encode($banners_arr);
?>
