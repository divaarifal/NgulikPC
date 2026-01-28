<?php
// Simple API Gateway in Native PHP

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

$request_uri = $_SERVER['REQUEST_URI'];
// Remove Query String for path parsing
$clean_uri = strtok($request_uri, '?');

// remove /NgulikPC/api_gateway
$path = str_replace('/NgulikPC/api_gateway', '', $clean_uri);
// FIX CASE SENSITIVITY
if($path == $clean_uri) {
    $path = str_ireplace('/NgulikPC/api_gateway', '', $clean_uri);
}

$parts = explode('/', trim($path, '/'));

$service = isset($parts[0]) ? $parts[0] : '';
$base_url = "http://localhost/NgulikPC";

$target_url = "";

// Route Mapping
switch($service) {
    case 'auth':
        $target_url = $base_url . "/auth_service/api/" . implode('/', array_slice($parts, 1)) . ".php";
        break;
    case 'catalog':
        $target_url = $base_url . "/catalog_service/api/" . implode('/', array_slice($parts, 1)). ".php";
        break;
    case 'orders':
        $target_url = $base_url . "/order_service/api/" . implode('/', array_slice($parts, 1)). ".php";
        break;
    case 'inventory':
        $target_url = $base_url . "/inventory_service/api/" . implode('/', array_slice($parts, 1)). ".php";
        break;
    case 'cms':
        $target_url = $base_url . "/cms_service/api/" . implode('/', array_slice($parts, 1)). ".php";
        break;
    default:
        http_response_code(404);
        echo json_encode(["message" => "Service not found in Gateway."]);
        exit;
}

// Append Query String
if(strpos($_SERVER['REQUEST_URI'], '?') !== false) {
    $target_url .= '?' . explode('?', $_SERVER['REQUEST_URI'])[1];
}

// FOR V1.1.0: Disable Token Check for simplicity in debugging CRUD
// In production we would uncomment this
/*
if($service == 'orders' || $service == 'inventory') {
    $headers = apache_request_headers();
    if(!isset($headers['Authorization'])) {
        // http_response_code(401);
        // echo json_encode(["message" => "Unauthorized. Token required."]);
    }
}
*/

// Forward Request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $target_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input_data = file_get_contents("php://input");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($input_data)
    ]);
}

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($http_code);
echo $response;
?>
