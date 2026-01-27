<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if(!$slug) header('Location: products.php');

// Fetch Product
$product = $api->get('/catalog/products/read_one?slug=' . $slug);

if(!$product || isset($product['message'])) {
    echo "Product not found";
    exit;
}

// Fetch Stock
$stock = $api->get('/inventory/stock/read?product_id=' . $product['id']);
$available_stock = ($stock && isset($stock['available'])) ? $stock['available'] : 0;

include 'includes/header.php';
?>

<div class="container mt-4 flex" style="gap: 4rem; flex-wrap: wrap;">
    <!-- Image -->
    <div class="animate-up" style="flex: 1; min-width: 300px;">
        <div class="glass-panel" style="padding: 1rem;">
            <img src="<?php echo (isset($product['images'][0]) ? $product['images'][0] : 'assets/images/placeholder_gpu.png'); ?>" style="width: 100%; border-radius: 12px;" alt="<?php echo $product['name']; ?>">
        </div>
    </div>

    <!-- Info -->
    <div class="animate-up delay-1" style="flex: 1; min-width: 300px;">
        <div style="color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">
            <?php echo $product['category_name']; ?> / <?php echo $product['brand']; ?>
        </div>
        <h1 style="font-size: 3rem; line-height: 1.1; margin-bottom: 1rem;"><?php echo $product['name']; ?></h1>
        
        <div class="flex items-center" style="gap: 2rem; margin-bottom: 2rem;">
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--accent);">
                Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
            </div>
            
            <?php if($available_stock > 0): ?>
                <div style="background: rgba(0, 242, 96, 0.2); color: #00f260; padding: 0.5rem 1rem; border-radius: 50px; font-weight: 600;">
                    In Stock: <?php echo $available_stock; ?>
                </div>
            <?php else: ?>
                <div style="background: rgba(245, 56, 68, 0.2); color: var(--primary); padding: 0.5rem 1rem; border-radius: 50px; font-weight: 600;">
                    Out of Stock
                </div>
            <?php endif; ?>
        </div>

        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.8; margin-bottom: 2rem;">
            <?php echo $product['description']; ?>
        </p>

        <form action="cart.php" method="POST" style="margin-bottom: 3rem;">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
            
            <div class="flex" style="gap: 1rem;">
                <input type="number" name="quantity" value="1" min="1" max="<?php echo $available_stock; ?>" class="form-input" style="width: 80px; text-align: center; font-size: 1.2rem; margin-bottom: 0;">
                <button type="submit" class="btn" <?php echo ($available_stock <= 0) ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>>
                    Add to Cart
                </button>
            </div>
        </form>

        <h3>Specifications</h3>
        <table class="specs-table">
            <?php if($product['specs']): ?>
                <?php foreach($product['specs'] as $key => $val): ?>
                    <tr>
                        <td style="color: var(--text-muted); text-transform: capitalize;"><?php echo str_replace('_', ' ', $key); ?></td>
                        <td><?php echo $val; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
