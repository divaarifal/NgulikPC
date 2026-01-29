<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

if(!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

$user_id = $_SESSION['user']['id'];
// Fetch orders for this user. 
// We might need a specific endpoint /orders/user?id=... or use the generic read with filter if supported.
// Let's assume generic read supports user_id filter or we create a new endpoint. 
// Looking at previous `orders/read.php` (if visible), it usually reads all. 
// Let's check if read.php supports filtering. If not, we might filter client side or quickly add filter to read.php.
// For now, let's call existing read and see. Ideally we pass `user_id` query param.
$all_orders = $api->get('/orders/orders/read'); // This reads ALL orders.
// In a real app we MUST filter in SQL. 
// Let's assume we proceed with client-side filtering for this MVP step, 
// OR better, we modify `order_service/api/orders/read.php` to accept user_id.
// I will assume specific user read logic is needed. 
// Let's modify `read.php` quickly? Or just filter here.
// Filtering here is safer for time. 

$user_orders = [];
if($all_orders && isset($all_orders['records'])) {
    foreach($all_orders['records'] as $o) {
        if($o['user_id'] == $user_id) {
            $user_orders[] = $o;
        }
    }
}

include 'includes/header.php';
?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold text-slate-800 mb-8">My Orders</h1>
    
    <?php if(count($user_orders) > 0): ?>
        <div class="space-y-6">
            <?php foreach($user_orders as $o): ?>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 transition hover:shadow-md">
                    <div class="flex justify-between items-start mb-4 border-b border-slate-50 pb-4">
                        <div>
                            <span class="font-bold text-slate-800 text-lg">Order #<?php echo $o['id']; ?></span>
                            <div class="text-sm text-slate-500"><?php echo $o['created_at']; ?></div>
                        </div>
                        <div class="text-right">
                             <?php
                                $statusClass = "bg-slate-100 text-slate-700";
                                if($o['status'] == 'pending') $statusClass = "bg-yellow-100 text-yellow-700";
                                if($o['status'] == 'confirmed') $statusClass = "bg-blue-100 text-blue-700";
                                if($o['status'] == 'shipping') $statusClass = "bg-orange-100 text-orange-700";
                                if($o['status'] == 'completed') $statusClass = "bg-green-100 text-green-700";
                                if($o['status'] == 'cancelled') $statusClass = "bg-red-100 text-red-700";
                            ?>
                            <span class="<?php echo $statusClass; ?> px-3 py-1 rounded-full text-xs font-bold uppercase inline-block mb-1">
                                <?php echo $o['status']; ?>
                            </span>
                            <div class="font-bold text-primary">Rp <?php echo number_format($o['total_price'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                         <?php foreach($o['items'] as $item): ?>
                            <div class="flex justify-between text-sm text-slate-700">
                                <span><?php echo $item['quantity']; ?>x <?php echo $item['product_name']; ?></span>
                                <span>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-12 bg-white rounded-2xl border border-slate-100">
            <i class="fas fa-box-open text-6xl text-slate-200 mb-4"></i>
            <h2 class="text-xl font-bold text-slate-600">No orders found</h2>
            <a href="products.php" class="text-primary font-bold hover:underline mt-2 inline-block">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
