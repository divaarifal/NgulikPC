<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

// Fetch Data Server-Side
$banners = $api->get('/cms/banners/read');
$categories = $api->get('/catalog/categories/read');
$newArrivals = $api->get('/catalog/products/read'); // Limit in real app

include 'includes/header.php';
?>

<!-- Hero -->
<section class="hero container glass-panel animate-up">
    <h1>Build Your <br><span style="color:white; -webkit-text-fill-color:white;">Dream Machine</span></h1>
    <p>Premium Components for High-End Gaming & Workstations</p>
    <a href="products.php" class="btn">Shop Now <i class="fas fa-arrow-right"></i></a>

    <!-- Simple Slider Placeholder (Real implementation would use SwiperJS or equiv) -->
    <div style="margin-top: 3rem; overflow: hidden; border-radius: 20px;">
        <?php if($banners && isset($banners['records']) && count($banners['records']) > 0): ?>
            <?php $firstBanner = $banners['records'][0]; ?>
            <img src="<?php echo $firstBanner['image_url']; ?>" style="width: 100%; max-height: 400px; object-fit: cover;" alt="Banner">
        <?php else: ?>
            <div style="height: 300px; background: #24243e; display: flex; align-items: center; justify-content: center;">
                <h2>Latest Tech Arrived</h2>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Categories -->
<section class="container mt-4 animate-up delay-1">
    <h2 class="text-center" style="margin-bottom: 2rem; font-size: 2.5rem;">Browse Categories</h2>
    <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));">
        <?php if($categories && isset($categories['records'])): ?>
            <?php foreach($categories['records'] as $cat): ?>
                <a href="products.php?category=<?php echo $cat['slug']; ?>" class="glass-panel" style="padding: 2rem; text-align: center; display: block; border: 1px solid var(--glass-border); transition: 0.3s;">
                    <i class="fas <?php echo $cat['icon']; ?>" style="font-size: 3rem; color: var(--secondary); margin-bottom: 1rem;"></i>
                    <h3 style="font-size: 1.2rem;"><?php echo $cat['name']; ?></h3>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Featured Products -->
<section class="container mt-4 animate-up delay-2">
    <div class="flex justify-between items-center" style="margin-bottom: 2rem;">
        <h2 style="font-size: 2.5rem;">New Arrivals</h2>
        <a href="products.php" style="color: var(--primary); font-weight: 700;">View All</a>
    </div>
    
    <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
        <?php if($newArrivals && isset($newArrivals['records'])): ?>
            <?php 
                // Show only first 4
                $count = 0;
                foreach($newArrivals['records'] as $p): 
                if($count >= 4) break;
                $count++;
            ?>
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
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
