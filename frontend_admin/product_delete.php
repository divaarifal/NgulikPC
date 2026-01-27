<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

// Auth check needed here

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $api->post('/catalog/products/delete', ['id' => $id]);
}

header('Location: index.php');
?>
