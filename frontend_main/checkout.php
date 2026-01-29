<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

if(!isset($_SESSION['user'])) { header('Location: login.php?redirect=checkout.php'); exit; }
if(!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) { header('Location: cart.php'); exit; }

$user = $_SESSION['user'];
$addresses = $api->get('/auth/addresses/read?user_id=' . $user['id']);

$success = false;
$error = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address_line = "";
    if($_POST['address_selection'] === 'new') {
        $address_line = $_POST['new_address'];
    } else {
        // Find saved address string (In real work we'd send ID, for now logic just takes text)
        // We'll trust the post value or re-fetch in real app
        $address_line = $_POST['selected_address_text']; 
    }

    if(empty($address_line)) {
        $error = "Please select or enter an address.";
    } else {
        $grandTotal = 0;
        foreach($_SESSION['cart'] as $item) $grandTotal += $item['price'] * $item['quantity'];

        $orderData = [
            'user_id' => $user['id'],
            'total_price' => $grandTotal,
            'shipping_address' => $address_line,
            'items' => $_SESSION['cart']
        ];

        // Ensure Router maps /orders/create correctly. 
        // Previously in index.php case 'orders': mapped to /order_service/api/orders/... 
        // Let's use direct path to be safe in `api_client` or verify gateway
        $result = $api->post('/orders/orders/create', $orderData, $_SESSION['token']); // Gateway routing '/orders' -> 'order_service'

        if(isset($result['order_id'])) {
            // Update Stocks
            foreach($_SESSION['cart'] as $item) {
                // Read-Write safe logic
                $currentStock = $api->get('/inventory/stock/read?product_id=' . $item['product_id']);
                if($currentStock && isset($currentStock['quantity'])) {
                    $newQty = $currentStock['quantity'] - $item['quantity'];
                    $api->post('/inventory/stock/update', ['product_id' => $item['product_id'], 'quantity' => $newQty]);
                }
            }
            unset($_SESSION['cart']);
            $success = true;
            $orderId = $result['order_id'];
        } else {
            $error = "Order failed. " . json_encode($result);
        }
    }
}

include 'includes/header.php';
?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold text-slate-800 mb-8">Checkout</h1>
    
    <?php if($success): ?>
        <div class="bg-white p-12 rounded-2xl border border-slate-100 shadow-xl text-center max-w-2xl mx-auto">
            <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="text-3xl font-bold text-slate-800 mb-4">Order Placed Successfully!</h2>
            <p class="text-slate-500 mb-8">Order ID: #<?php echo $orderId; ?>. You can view it in your profile.</p>
            <a href="index.php" class="px-8 py-3 bg-primary text-white rounded-full font-bold shadow-lg hover:bg-red-600 transition">Back to Home</a>
        </div>
    <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left: Address & User -->
            <div class="w-full lg:w-2/3">
                <?php if($error): ?>
                    <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm mb-6">
                    <h3 class="font-bold text-lg mb-4 text-slate-800 border-b pb-2">Customer Information</h3>
                    <div class="flex items-center gap-4">
                        <img src="<?php echo isset($user['avatar']) ? $user['avatar'] : 'assets/images/default_avatar.png'; ?>" class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-800"><?php echo htmlspecialchars($user['username']); ?></div>
                            <div class="text-sm text-slate-500"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                    </div>
                </div>

                <form id="checkoutForm" method="POST" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="font-bold text-lg mb-4 text-slate-800 border-b pb-2">Shipping Address</h3>
                    
                    <div class="space-y-4 mb-4">
                        <?php if($addresses && isset($addresses['records']) && count($addresses['records']) > 0): ?>
                            <?php foreach($addresses['records'] as $index => $addr): ?>
                                <label class="flex items-start gap-3 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-primary transition bg-slate-50">
                                    <input type="radio" name="address_selection" value="saved_<?php echo $index; ?>" class="mt-1" <?php echo ($index===0)?'checked':''; ?> onclick="setAddress('<?php echo htmlspecialchars($addr['address_line']); ?>')">
                                    <div>
                                        <div class="font-bold text-slate-800"><?php echo $addr['label']; ?></div>
                                        <div class="text-sm text-slate-600"><?php echo $addr['recipient_name']; ?> (<?php echo $addr['phone_number']; ?>)</div>
                                        <div class="text-sm text-slate-500"><?php echo $addr['address_line']; ?></div>
                                    </div>
                                </label>
                                <?php if($index===0) $firstAddr = $addr['address_line']; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <label class="flex items-start gap-3 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-primary transition bg-slate-50">
                            <input type="radio" name="address_selection" value="new" onclick="document.getElementById('newAddressBox').classList.remove('hidden'); document.getElementById('selectedAddressInput').value = '';">
                            <span class="font-bold text-slate-700">Ship to a different address</span>
                        </label>
                    </div>

                    <div id="newAddressBox" class="hidden">
                        <textarea name="new_address" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2" rows="3" placeholder="Enter full address..."></textarea>
                    </div>
                    
                    <input type="hidden" name="selected_address_text" id="selectedAddressInput" value="<?php echo isset($firstAddr) ? $firstAddr : ''; ?>">
                </form>
            </div>

            <!-- Right: Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm sticky top-24">
                    <h3 class="font-bold text-lg mb-4 text-slate-800">Order Summary</h3>
                    
                    <div class="max-h-60 overflow-y-auto mb-4 space-y-3 custom-scrollbar">
                        <?php 
                        $grandTotal = 0;
                        foreach($_SESSION['cart'] as $item): 
                            $total = $item['price'] * $item['quantity'];
                            $grandTotal += $total;
                        ?>
                        <div class="flex justify-between items-center text-sm">
                            <div class="text-slate-600">
                                <span class="font-bold text-slate-800"><?php echo $item['quantity']; ?>x</span> <?php echo $item['name']; ?>
                            </div>
                            <div class="font-bold text-slate-800">Rp <?php echo number_format($total, 0, ',', '.'); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="border-t border-slate-100 pt-4 mt-4">
                        <div class="flex justify-between items-center text-lg mb-6">
                            <span class="font-bold text-slate-600">Total</span>
                            <span class="font-bold text-primary text-2xl">Rp <?php echo number_format($grandTotal, 0, ',', '.'); ?></span>
                        </div>
                        
                        <button onclick="document.getElementById('checkoutForm').submit()" class="w-full bg-primary text-white py-4 rounded-xl font-bold shadow-lg shadow-red-500/30 hover:bg-red-600 transition">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function setAddress(addr) {
        document.getElementById('newAddressBox').classList.add('hidden');
        document.getElementById('selectedAddressInput').value = addr;
    }
</script>

<?php include 'includes/footer.php'; ?>
