<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

// In real app, check if user is admin
// if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') { header('Location: ../frontend_main/login.php'); exit; }

$products = $api->get('/catalog/products/read');
$categories = $api->get('/catalog/categories/read');

include '../frontend_main/includes/header.php'; // Reuse header for consistent look, or make a custom one
?>

<div class="container mt-4 animate-up">
    <div class="flex justify-between items-center" style="margin-bottom: 2rem;">
        <h1>Admin Dashboard</h1>
        <a href="product_form.php" class="btn"><i class="fas fa-plus"></i> Add Product</a>
    </div>

    <!-- Stats or other info -->
    <div class="grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 3rem;">
        <div class="glass-panel" style="padding: 1.5rem;">
            <h3>Total Products</h3>
            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;"><?php echo count($products['records']); ?></div>
        </div>
        <!-- More stats placeholders -->
    </div>

    <h2>Product Management</h2>
    <div class="glass-panel" style="padding: 0; overflow: hidden; margin-top: 1rem;">
        <table class="specs-table" style="margin: 0;">
            <thead>
                <tr style="background: rgba(255,255,255,0.05);">
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if($products && isset($products['records'])): ?>
                    <?php foreach($products['records'] as $p): ?>
                        <tr>
                            <td><?php echo $p['id']; ?></td>
                            <td>
                                <img src="<?php echo (isset($p['images'][0]) ? $p['images'][0] : '../frontend_main/assets/images/placeholder_gpu.png'); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                            </td>
                            <td><?php echo $p['name']; ?></td>
                            <td>Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></td>
                            <td><?php echo $p['category_name']; ?></td>
                            <td>
                                <a href="product_form.php?id=<?php echo $p['slug']; ?>" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: var(--secondary);">Edit</a>
                                <a href="product_delete.php?id=<?php echo $p['id']; ?>" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #e74c3c;" onclick="return confirm('Delete this product?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../frontend_main/includes/footer.php'; ?>
