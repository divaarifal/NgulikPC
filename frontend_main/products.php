<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

// Query Params
$category_slug = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build Query String
$query = "?";
if($category_slug) $query .= "category=" . $category_slug . "&";
if($search) $query .= "search=" . $search;

$products = $api->get('/catalog/products/read' . $query);
$categories = $api->get('/catalog/categories/read');

include 'includes/header.php';
?>

<div class="container mt-2 flex" style="gap: 2rem; align-items: flex-start;">
    <!-- Sidebar -->
    <aside class="glass-panel animate-up" style="width: 250px; padding: 1.5rem; position: sticky; top: 100px;">
        <form action="products.php" method="GET">
            <h3 style="margin-bottom: 1rem;">Search</h3>
            <input type="text" name="search" class="form-input" placeholder="Keyword..." value="<?php echo htmlspecialchars($search); ?>">
            
            <h3 style="margin: 1.5rem 0 1rem;">Categories</h3>
            <ul style="padding-left: 0;">
                <li style="margin-bottom: 0.5rem;"><a href="products.php" style="<?php echo ($category_slug == '') ? 'color: var(--primary); font-weight: bold;' : 'color: var(--text-muted);' ?>">All Products</a></li>
                <?php if($categories && isset($categories['records'])): ?>
                    <?php foreach($categories['records'] as $cat): ?>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="products.php?category=<?php echo $cat['slug']; ?>" style="<?php echo ($category_slug == $cat['slug']) ? 'color: var(--primary); font-weight: bold;' : 'color: var(--text-muted);' ?>">
                                <i class="fas <?php echo $cat['icon']; ?>" style="width: 20px;"></i> <?php echo $cat['name']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </form>
    </aside>

    <!-- Main Content -->
    <div style="flex: 1;">
        <h1 class="animate-up" style="margin-bottom: 2rem;">Shop Products</h1>
        
        <?php if($products && isset($products['records']) && count($products['records']) > 0): ?>
            <div class="grid animate-up delay-1" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
                <?php foreach($products['records'] as $p): ?>
                    <div class="card glass-panel">
                        <img src="<?php echo (isset($p['images'][0]) ? $p['images'][0] : 'assets/images/placeholder_gpu.png'); ?>" alt="<?php echo $p['name']; ?>">
                        <div class="card-body">
                            <div style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase;"><?php echo $p['category_name']; ?></div>
                            <h3 class="card-title"><?php echo $p['name']; ?></h3>
                            <div class="card-price">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></div>
                            <a href="product-detail.php?slug=<?php echo $p['slug']; ?>" class="btn" style="width: 100%; margin-top: 1rem; display: block; text-align: center;">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="glass-panel" style="padding: 3rem; text-align: center;">
                <h2>No products found</h2>
                <p>Try adjusting your search or filter.</p>
                <a href="products.php" class="btn mt-2">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
