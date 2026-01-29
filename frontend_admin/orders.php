<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

$orders = $api->get('/orders/orders/read'); 

include 'includes/admin_header.php';
?>

<div class="container mx-auto">
    <h1 class="text-3xl font-bold mb-8">Order Management</h1>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="p-4">Order ID</th>
                    <th class="p-4">Date</th>
                    <th class="p-4">Customer ID</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Items</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if($orders && isset($orders['records'])): ?>
                    <?php foreach($orders['records'] as $o): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-bold text-slate-800">#<?php echo $o['id']; ?></td>
                            <td class="p-4 text-slate-500 text-sm"><?php echo $o['created_at']; ?></td>
                            <td class="p-4 text-slate-600"><?php echo $o['user_id']; ?></td>
                            <td class="p-4 font-bold text-slate-800">Rp <?php echo number_format($o['total_price'], 0, ',', '.'); ?></td>
                            <td class="p-4">
                                <?php
                                    // Normalize status for comparison
                                    $rawStatus = isset($o['status']) ? strtolower(trim($o['status'])) : '';
                                    
                                    $statusLabel = $o['status']; // Default to raw
                                    $statusClass = "bg-slate-100 text-slate-700";
                                    
                                    if($rawStatus == 'pending') {
                                        $statusClass = "bg-yellow-100 text-yellow-700";
                                        $statusLabel = "Menunggu Konfirmasi";
                                    }
                                    if($rawStatus == 'confirmed') {
                                        $statusClass = "bg-blue-100 text-blue-700";
                                        $statusLabel = "Memproses Pesanan"; 
                                    } 
                                    if($rawStatus == 'shipping' || $rawStatus == 'shipped') {
                                        $statusClass = "bg-orange-100 text-orange-700";
                                        $statusLabel = "Sedang Diantar";
                                    }
                                    if($rawStatus == 'completed') {
                                        $statusClass = "bg-green-100 text-green-700";
                                        $statusLabel = "Selesai";
                                    }
                                    if($rawStatus == 'cancelled') {
                                        $statusClass = "bg-red-100 text-red-700";
                                        $statusLabel = "Batal";
                                    }
                                ?>
                                <span class="<?php echo $statusClass; ?> py-1 px-3 rounded-full text-xs font-bold uppercase">
                                    <?php echo $statusLabel; ?>
                                </span>
                                <div class="mt-2 flex gap-1 flex-wrap">
                                    <?php if($rawStatus == 'pending' || $rawStatus == 'paid'): ?>
                                        <a href="order_status_update.php?id=<?php echo $o['id']; ?>&status=confirmed" onclick="return confirm('Konfirmasi Pesanan ini?')" class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 inline-block">Konfirmasi Pesanan</a>
                                        <a href="order_status_update.php?id=<?php echo $o['id']; ?>&status=cancelled" onclick="return confirm('Batalkan Pesanan ini?')" class="text-xs bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600 inline-block">Batal</a>
                                    <?php endif; ?>
                                    
                                    <?php if($rawStatus == 'confirmed'): ?>
                                        <a href="order_status_update.php?id=<?php echo $o['id']; ?>&status=shipping" onclick="return confirm('Set status ke Diantar?')" class="text-xs bg-orange-500 text-white px-2 py-1 rounded hover:bg-orange-600 inline-block">Pesanan sedang diantar</a>
                                    <?php endif; ?>

                                    <?php if($rawStatus == 'shipping' || $rawStatus == 'shipped'): ?>
                                        <a href="order_status_update.php?id=<?php echo $o['id']; ?>&status=completed" onclick="return confirm('Selesaikan Pesanan ini?')" class="text-xs bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 inline-block">Pesanan Selesai</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="p-4">
                                <ul class="list-disc list-inside text-sm text-slate-600">
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

</main>
<script>
async function updateStatus(orderId, newStatus) {
    if(!confirm('Change status to ' + newStatus + '?')) return;
    
    // We need to call the Order Service directly or via Gateway.
    // Gateway Route: /orders/orders/update_status (Assumed mapping)
    // Or direct PHP if we used that in checkout. let's try direct API client format or fetch.
    
    // Since we are in frontend_admin, we can't easily use the PHP ApiClient class for AJAX without a wrapper.
    // Let's rely on a simple fetch to the gateway if possible, or create a helper php file.
    // For simplicity efficiently, let's just POST to a self-handler or generic ajax endpoint.
    // Actually, we can just use `window.location` to a helper php script 'order_status_update.php?id=...&status=...' 
    
    window.location.href = `order_status_update.php?id=${orderId}&status=${newStatus}`;
}
</script>
</body>
</html>
