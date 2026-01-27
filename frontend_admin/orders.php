<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

$orders = $api->get('/order_service/api/orders/read'); // Fixed path or via gateway /orders/read if mapped

include '../frontend_main/includes/header.php';
?>

<div class="container mt-4 animate-up">
    <h1>Order Management</h1>
    
    <div class="glass-panel" style="padding: 0; overflow: hidden; margin-top: 1rem;">
        <table class="specs-table" style="margin: 0;">
            <thead>
                <tr style="background: rgba(255,255,255,0.05);">
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Customer (ID)</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                <?php if($orders && isset($orders['records'])): ?>
                    <?php foreach($orders['records'] as $o): ?>
                        <tr>
                            <td>#<?php echo $o['id']; ?></td>
                            <td><?php echo $o['created_at']; ?></td>
                            <td><?php echo $o['user_id']; ?></td>
                            <td>Rp <?php echo number_format($o['total_price'], 0, ',', '.'); ?></td>
                            <td>
                                <span style="background: var(--accent); color: #000; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                                    <?php echo $o['status']; ?>
                                </span>
                            </td>
                            <td>
                                <ul>
                                <?php foreach($o['items'] as $item): ?>
                                    <li><?php echo $item['quantity']; ?>x <?php echo $item['product_name']; ?></li>
                                <?php endforeach; ?>
                                </ul>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../frontend_main/includes/footer.php'; ?>
