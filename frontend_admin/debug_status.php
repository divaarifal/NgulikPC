<?php
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

$id = isset($_GET['id']) ? $_GET['id'] : 1; 
$status = isset($_GET['status']) ? $_GET['status'] : 'confirmed';

echo "Testing Update Order #$id to $status<br>";

$payload = ['order_id' => $id, 'status' => $status];
echo "Payload: " . json_encode($payload) . "<br>";

$res = $api->post('/orders/orders/update_status', $payload);

echo "<pre>";
print_r($res);
echo "</pre>";

echo "Fetching Order Info...<br>";
$order = $api->get('/orders/orders/read_one?id=' . $id);
echo "<pre>";
print_r($order);
echo "</pre>";
?>
