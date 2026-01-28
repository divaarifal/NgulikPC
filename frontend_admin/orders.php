<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

$orders = $api->get('/order_service/api/orders/read'); 

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
                                    $statusClass = "bg-slate-100 text-slate-700";
                                    if($o['status'] == 'pending') $statusClass = "bg-yellow-100 text-yellow-700";
                                    if($o['status'] == 'paid') $statusClass = "bg-green-100 text-green-700";
                                    if($o['status'] == 'shipped') $statusClass = "bg-blue-100 text-blue-700";
                                    if($o['status'] == 'cancelled') $statusClass = "bg-red-100 text-red-700";
                                ?>
                                <span class="<?php echo $statusClass; ?> py-1 px-3 rounded-full text-xs font-bold uppercase">
                                    <?php echo $o['status']; ?>
                                </span>
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
</div>
</body>
</html>
