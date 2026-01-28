<?php
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

echo "Testing Stock Update for Product ID 1...\n";

// 1. Read Current
$data = $api->get('/inventory/stock/read?product_id=1');
echo "Current Stock: " . print_r($data, true) . "\n";

// 2. Update to 999
echo "Updating to 999...\n";
$res = $api->post('/inventory/stock/update', ['product_id' => 1, 'quantity' => 999]);
echo "Update Result: " . print_r($res, true) . "\n";

// 3. Read Again
$data2 = $api->get('/inventory/stock/read?product_id=1');
echo "New Stock: " . print_r($data2, true) . "\n";
?>
