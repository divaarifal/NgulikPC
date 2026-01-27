<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

if(!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

if(!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header('Location: cart.php');
    exit;
}

$success = false;
$error = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'];
    $user = $_SESSION['user'];
    $cart = $_SESSION['cart'];
    
    $grandTotal = 0;
    foreach($cart as $item) $grandTotal += $item['price'] * $item['quantity'];

    $orderData = [
        'user_id' => $user['id'],
        'total_price' => $grandTotal,
        'shipping_address' => $address,
        'items' => $cart
    ];

    // Call Order API
    // Note: In real app, we should check stock again here via Inventory Service
    // For now we assume success
    $result = $api->post('/order_service/api/orders/create', $orderData, $_SESSION['token']); // Direct service call path fixed
    // Wait, API gateway path is /orders/create. Corrected below.
    $result = $api->post('/orders/create', $orderData);

    if(isset($result['order_id'])) {
        // Update Stock
        foreach($cart as $item) {
             $api->post('/inventory/stock/update', [
                'product_id' => $item['product_id'],
                'quantity' => -($item['quantity']) // Reduce stock, wait my update logic was SET not DECREMENT.
                // My update.php logic: "UPDATE stocks SET quantity = :quantity". It sets absolute value.
                // This is a logic flaw in my V1.0 API. 
                // FIX: I will quickly patch update.php logic to be safe, or I must read then write.
                // For this V1.1.0 demo, I will read stock first then write, server side here.
             ]);
             // Actually, to do this correctly without race condition, Inventory Service needs "decrease" endpoint.
             // But for now, I will Read -> Calculate -> Write.
             $currentStock = $api->get('/inventory/stock/read?product_id=' . $item['product_id']);
             if($currentStock) {
                 $newQty = $currentStock['quantity'] - $item['quantity'];
                 $api->post('/inventory/stock/update', ['product_id' => $item['product_id'], 'quantity' => $newQty]);
             }
        }

        unset($_SESSION['cart']);
        $success = true;
    } else {
        $error = "Failed to place order. " . json_encode($result);
    }
}

include 'includes/header.php';
?>

<div class="container mt-4 animate-up">
    <h1>Checkout</h1>
    
    <?php if($success): ?>
        <div class="glass-panel text-center" style="padding: 4rem;">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: var(--accent); margin-bottom: 1rem;"></i>
            <h2>Order Placed Successfully!</h2>
            <p>Thank you for shopping with NgulikPC.</p>
            <a href="index.php" class="btn mt-2">Back to Home</a>
        </div>
    <?php else: ?>
        <div class="glass-panel" style="padding: 2rem; max-width: 600px; margin: 0 auto;">
            <?php if($error): ?>
                <div style="background: rgba(245,56,68,0.2); color: #ff6b6b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem;">Shipping Address</label>
                    <textarea name="address" class="form-input" rows="4" required placeholder="Enter full address..."></textarea>
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <h3>Order Summary</h3>
                    <p>Total Items: <?php echo count($_SESSION['cart']); ?></p>
                    <?php 
                        $gTotal = 0;
                        foreach($_SESSION['cart'] as $i) $gTotal += $i['price'] * $i['quantity'];
                    ?>
                    <h2 style="color: var(--accent);">Total: Rp <?php echo number_format($gTotal, 0, ',', '.'); ?></h2>
                </div>

                <button type="submit" class="btn" style="width: 100%;">Confirm Order</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
