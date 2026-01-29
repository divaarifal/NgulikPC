<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

if(isset($_GET['id']) && isset($_GET['status'])) {
    $payload = [
        'order_id' => $_GET['id'],
        'status' => $_GET['status']
    ];
    
    // Call Order Service
    // Route: /orders/orders/update_status
    // Assuming gateway maps /orders -> order_service
    $result = $api->post('/orders/orders/update_status', $payload);
    

    
    if(isset($result['message']) && $result['message'] == "Order status updated.") {
        header('Location: orders.php');
        exit;
    } else {
        echo "Error: ";
        print_r($result);
        echo "<br><a href='orders.php'>Back</a>";
        exit;
    }
}

header('Location: orders.php?error=invalid_request');
?>
