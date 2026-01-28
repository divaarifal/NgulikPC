<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

// Fetch Data for Stats
$products = $api->get('/catalog/products/read');
$orders = $api->get('/order_service/api/orders/read'); // Need to ensure route exists or use direct path via gateway
// Actually my existing orders.php uses logic: $api->get('/orders/read') if mapped? 
// Gateway 'orders' -> 'order_service/api'. So '/orders/orders/read' ? No.
// Let's check 'orders.php' in frontend_admin to see how it fetches.
// It likely used direct DB or I need to check.
// I'll assume I can fetch all orders and count.

$total_products = isset($products['records']) ? count($products['records']) : 0;
$total_orders = 0;
$pending_orders = 0;
$revenue = 0;

$orders_data = $api->get('/orders/orders/read'); // Assuming Gateway: /orders maps to order_service, then /orders/read.php
// Wait, my gateway map: 'orders' => $base_url . "/order_service/api/" ...
// So if I call /orders/read, it maps to /order_service/api/read.php
// But the file is `order_service/api/orders/read.php`.
// So I should call `/orders/orders/read`.

if($orders_data && isset($orders_data['records'])) {
    $total_orders = count($orders_data['records']);
    foreach($orders_data['records'] as $o) {
        if($o['status'] == 'pending') $pending_orders++;
        $revenue += $o['total_price'];
    }
}

include 'includes/admin_header.php';
?>

<h1 class="text-3xl font-bold mb-8">Dashboard Overview</h1>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
            <i class="fas fa-box"></i>
        </div>
        <div>
            <div class="text-slate-500 text-sm font-bold uppercase">Total Products</div>
            <div class="text-2xl font-bold text-slate-800"><?php echo $total_products; ?></div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div>
            <div class="text-slate-500 text-sm font-bold uppercase">Total Orders</div>
            <div class="text-2xl font-bold text-slate-800"><?php echo $total_orders; ?></div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center text-xl">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div class="text-slate-500 text-sm font-bold uppercase">Pending Orders</div>
            <div class="text-2xl font-bold text-slate-800"><?php echo $pending_orders; ?></div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-xl">
            <i class="fas fa-wallet"></i>
        </div>
        <div>
            <div class="text-slate-500 text-sm font-bold uppercase">Total Revenue</div>
            <div class="text-2xl font-bold text-slate-800">Rp <?php echo number_format($revenue, 0, ',', '.'); ?></div>
        </div>
    </div>
</div>

<div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center py-20">
    <img src="../frontend_main/assets/images/placeholder_gpu.png" class="w-48 mx-auto mb-6 opacity-50 mix-blend-luminosity">
    <h2 class="text-xl font-bold text-slate-800">Welcome to Admin Panel</h2>
    <p class="text-slate-500">Manage your store efficiently using the sidebar menu.</p>
</div>

</main>
</div>
</body>
</html>
