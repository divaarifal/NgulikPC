<?php
include 'includes/api_client.php';
$api = new ApiClient();

echo "<h1>Stock Debug</h1>";

$pid = 1; // Assuming product ID 1 exists
echo "Testing Product ID: $pid<br>";

// 1. Direct Gateway Call via cURL (ApiClient does this)
$url = '/inventory/stock/read?product_id=' . $pid;
echo "Calling: $url <br>";

$res = $api->get($url);

echo "<pre>";
print_r($res);
echo "</pre>";

if(isset($res['available'])) {
    echo "Available: " . $res['available'];
} else {
    echo "Failed to get available count.";
}
?>
