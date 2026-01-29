<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$category_slug = isset($_GET['category']) ? $_GET['category'] : "";
$search = isset($_GET['search']) ? $_GET['search'] : "";
$brand = isset($_GET['brand']) ? $_GET['brand'] : "";

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE 1=1";

$params = [];

if(!empty($category_slug)){
    $query .= " AND c.slug = ?";
    $params[] = $category_slug;
}

if(!empty($brand)){
    $query .= " AND p.brand = ?";
    $params[] = $brand;
}

if(!empty($search)){
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : "newest"; // newest, name_asc, name_desc, price_asc, price_desc

switch($sort) {
    case 'name_asc':
        $query .= " ORDER BY p.name ASC";
        break;
    case 'name_desc':
        $query .= " ORDER BY p.name DESC";
        break;
    case 'price_asc':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.price DESC";
        break;
    default: // newest
        $query .= " ORDER BY p.created_at DESC";
        break;
}

$stmt = $db->prepare($query);
$stmt->execute($params);

$products_arr = array();
$products_arr["records"] = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
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
    array_push($products_arr["records"], $product_item);
}

http_response_code(200);
echo json_encode($products_arr);
?>
