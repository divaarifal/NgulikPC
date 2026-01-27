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

<div class="container mt-4 animate-up">
    <h1>Your Shopping Cart</h1>
    
    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <div class="glass-panel" style="padding: 2rem;">
            <table class="specs-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Product</th>
                        <th style="text-align: center;">Price</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    foreach($_SESSION['cart'] as $item): 
                        $total = $item['price'] * $item['quantity'];
                        $grandTotal += $total;
                    ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td style="text-align: center;">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                        <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align: right;">Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                        <td style="text-align: right;">
                            <a href="cart.php?remove=<?php echo $item['product_id']; ?>" style="color: var(--primary);"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="flex justify-between items-center" style="margin-top: 2rem; border-top: 1px solid var(--glass-border); padding-top: 2rem;">
                <h3>Grand Total</h3>
                <h2 style="color: var(--accent);">Rp <?php echo number_format($grandTotal, 0, ',', '.'); ?></h2>
            </div>

            <div class="text-center" style="margin-top: 2rem;">
                <a href="checkout.php" class="btn">Proceed to Checkout</a>
            </div>
        </div>
    <?php else: ?>
        <div class="glass-panel text-center" style="padding: 4rem;">
            <h2>Your cart is empty</h2>
            <a href="products.php" class="btn mt-2">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
