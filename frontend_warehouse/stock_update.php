<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['product_id'];
    $qty = (int)$_POST['quantity'];

    $api->post('/inventory/stock/update', ['product_id' => $id, 'quantity' => $qty]);
}

header('Location: index.php');
?>
