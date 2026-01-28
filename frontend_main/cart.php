<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if($_POST['action'] === 'add') {
        $id = $_POST['product_id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $qty = (int)$_POST['quantity'];
        
        // Initialize cart if not exists
        if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        // Check if item exists
        $found = false;
        foreach($_SESSION['cart'] as &$item) {
            if($item['product_id'] == $id) {
                $item['quantity'] += $qty;
                $found = true;
                break;
            }
        }
        
        if(!$found) {
            $_SESSION['cart'][] = [
                'product_id' => $id,
                'name' => $name,
                'price' => $price,
                'quantity' => $qty
            ];
        }
    }
    
    // Redirect back
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    foreach($_SESSION['cart'] as $key => $item) {
        if($item['product_id'] == $removeId) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header('Location: cart.php');
    exit;
}

include 'includes/header.php';
?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold text-slate-800 mb-8">Your Shopping Cart</h1>
    
    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-3/4">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs">
                            <tr>
                                <th class="p-6">Product</th>
                                <th class="p-6 text-center">Qty</th>
                                <th class="p-6 text-right">Price</th>
                                <th class="p-6 text-right">Total</th>
                                <th class="p-6"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php 
                            $grandTotal = 0;
                            foreach($_SESSION['cart'] as $item): 
                                $total = $item['price'] * $item['quantity'];
                                $grandTotal += $total;
                            ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-6 font-bold text-slate-800"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td class="p-6 text-center"><?php echo $item['quantity']; ?></td>
                                <td class="p-6 text-right text-slate-500">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td class="p-6 text-right font-bold text-slate-800">Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                                <td class="p-6 text-right">
                                    <a href="cart.php?remove=<?php echo $item['product_id']; ?>" class="text-red-400 hover:text-red-600 transition"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="w-full lg:w-1/4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-800 text-lg mb-4">Cart Summary</h3>
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-slate-500">Subtotal</span>
                        <span class="font-bold text-slate-800">Rp <?php echo number_format($grandTotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="flex justify-between items-center mb-8 text-xl">
                        <span class="font-bold text-slate-800">Total</span>
                        <span class="font-bold text-primary">Rp <?php echo number_format($grandTotal, 0, ',', '.'); ?></span>
                    </div>
                    <a href="checkout.php" class="block w-full text-center bg-primary text-white py-3 rounded-xl font-bold shadow-lg shadow-red-500/30 hover:bg-red-600 transition">Proceed to Checkout</a>
                    <a href="products.php" class="block w-full text-center mt-4 text-slate-500 hover:text-slate-800 text-sm font-bold">Continue Shopping</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white p-12 rounded-2xl border border-slate-100 shadow-sm text-center">
            <i class="fas fa-shopping-cart text-6xl text-slate-200 mb-6"></i>
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Your cart is empty</h2>
            <p class="text-slate-500 mb-8">Looks like you haven't added anything yet.</p>
            <a href="products.php" class="inline-block px-8 py-3 bg-primary text-white rounded-full font-bold hover:bg-red-600 transition">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
