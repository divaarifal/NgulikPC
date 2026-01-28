<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

$products = $api->get('/catalog/products/read');

include 'includes/admin_header.php';
?>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold">Product Catalog</h1>
    <a href="product_form.php" class="bg-primary text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-red-600 transition"><i class="fas fa-plus mr-2"></i> Add Product</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs">
            <tr>
                <th class="p-4">Image</th>
                <th class="p-4">Product Name</th>
                <th class="p-4">Price</th>
                <th class="p-4">Category</th>
                <th class="p-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if($products && isset($products['records'])): ?>
                <?php foreach($products['records'] as $p): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4">
                             <img src="<?php echo (isset($p['images'][0]) ? $p['images'][0] : '../frontend_main/assets/images/placeholder_gpu.png'); ?>" class="w-12 h-12 rounded-lg object-cover">
                        </td>
                        <td class="p-4 font-bold text-slate-800"><?php echo $p['name']; ?></td>
                        <td class="p-4 text-slate-600 font-medium">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></td>
                        <td class="p-4">
                            <span class="bg-blue-100 text-blue-700 py-1 px-3 rounded-full text-xs font-bold uppercase"><?php echo $p['category_name']; ?></span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="product_form.php?id=<?php echo $p['slug']; ?>" class="text-blue-500 hover:text-blue-700 mr-4"><i class="fas fa-edit"></i></a>
                            <a href="product_delete.php?id=<?php echo $p['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this product?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</main>
</div>
</body>
</html>
