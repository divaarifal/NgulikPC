<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, name, slug, icon FROM categories ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();

$categories_arr = array();
$categories_arr["records"] = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    $category_item = array(
        "id" => $id,
        "name" => $name,
        "slug" => $slug,
        "icon" => $icon
    );
    array_push($categories_arr["records"], $category_item);
}

http_response_code(200);
echo json_encode($categories_arr);
?>
