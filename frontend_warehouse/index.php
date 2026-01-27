<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

// Auth check needed

$products = $api->get('/catalog/products/read');

include '../frontend_main/includes/header.php'; 
?>

<div class="container mt-4 animate-up">
    <h1>Warehouse Stock Management</h1>
    
    <div class="grid mt-2" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
        <?php if($products && isset($products['records'])): ?>
            <?php foreach($products['records'] as $p): ?>
                <?php 
                    // Fetch stock individually (N+1 prob, but okay for v1.1 demo)
                    $stock = $api->get('/inventory/stock/read?product_id=' . $p['id']);
                    $qty = ($stock && isset($stock['quantity'])) ? $stock['quantity'] : 0;
                    $reserved = ($stock && isset($stock['reserved'])) ? $stock['reserved'] : 0;
                ?>
                <div class="glass-panel" style="padding: 1.5rem;">
                    <div style="font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase;">ID: <?php echo $p['id']; ?></div>
                    <h3 style="margin-bottom: 1rem;"><?php echo $p['name']; ?></h3>
                    
                    <div class="flex justify-between items-center" style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Total Physical</div>
                            <div style="font-size: 1.5rem; font-weight: bold;"><?php echo $qty; ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Reserved</div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: var(--primary);"><?php echo $reserved; ?></div>
                        </div>
                    </div>

                    <form action="stock_update.php" method="POST" class="flex" style="gap: 0.5rem;">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <input type="number" name="quantity" value="<?php echo $qty; ?>" class="form-input" style="margin:0; text-align: center;">
                        <button type="submit" class="btn" style="padding: 0 1rem;">Update</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../frontend_main/includes/footer.php'; ?>
