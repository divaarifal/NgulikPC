<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

$isEdit = false;
$product = [
    'name' => '', 'price' => '', 'description' => '', 'category_id' => '', 'brand' => '', 'slug' => ''
];

$categories = $api->get('/catalog/categories/read');

if(isset($_GET['id'])) {
    $isEdit = true;
    $slugInfo = $_GET['id'];
    $data = $api->get('/catalog/products/read_one?slug=' . $slugInfo);
    if($data && !isset($data['message'])) {
        $product = $data;
    }
}

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [
        'name' => $_POST['name'],
        'price' => (int)$_POST['price'],
        'description' => $_POST['description'],
        'category_id' => (int)$_POST['category_id'],
        'brand' => $_POST['brand']
    ];

    if(isset($_POST['id']) && $_POST['id']) {
        // ... (Existing update logic, check files)
        // If file uploaded, upload it, get path, add to payload['images']
        if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
             $file = new CURLFile($_FILES['product_image']['tmp_name'], $_FILES['product_image']['type'], $_FILES['product_image']['name']);
             $up_res = $api->post('/catalog/products/upload_image', ['product_image' => $file], null, true);
             if(isset($up_res['path'])) {
                 $payload['images'] = [$up_res['path']]; // Overwriting images for now. In real app, we might append.
             }
        }
        
        $payload['id'] = $_POST['id'];
        $res = $api->post('/catalog/products/update', $payload);
        // ...
    } else {
        // Create Logic
         if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
             $file = new CURLFile($_FILES['product_image']['tmp_name'], $_FILES['product_image']['type'], $_FILES['product_image']['name']);
             $up_res = $api->post('/catalog/products/upload_image', ['product_image' => $file], null, true);
             if(isset($up_res['path'])) {
                 $payload['images'] = [$up_res['path']];
             }
        }
        
        $res = $api->post('/catalog/products/create', $payload);
        // ...
    }
}

include 'includes/admin_header.php';
?>

<div class="flex items-center gap-4 mb-8">
    <a href="index.php" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-500 hover:text-primary shadow-sm"><i class="fas fa-arrow-left"></i></a>
    <h1 class="text-3xl font-bold"><?php echo $isEdit ? 'Edit Product' : 'Add New Product'; ?></h1>
</div>

<?php if($error): ?>
    <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6"><?php echo $error; ?></div>
<?php endif; ?>
<?php if($success): ?>
    <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6"><?php echo $success; ?></div>
<?php endif; ?>

<div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 max-w-3xl">
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php if($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
             <div class="flex items-center gap-4 mb-4">
                <?php if(isset($product['images'][0])): ?>
                    <img src="<?php echo $product['images'][0]; ?>" class="w-20 h-20 rounded-lg object-cover border border-slate-200">
                <?php endif; ?>
                <div>
                     <label class="block font-bold mb-2 text-slate-700">Change Product Image</label>
                     <input type="file" name="product_image" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50">
                </div>
            </div>
        <?php else: ?>
            <div class="mb-4">
                 <label class="block font-bold mb-2 text-slate-700">Product Image</label>
                 <input type="file" name="product_image" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50">
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block font-bold mb-2 text-slate-700">Product Name</label>
                <input type="text" name="name" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50 focus:border-primary outline-none" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div>
                <label class="block font-bold mb-2 text-slate-700">Brand</label>
                <input type="text" name="brand" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50 focus:border-primary outline-none" value="<?php echo htmlspecialchars($product['brand']); ?>" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block font-bold mb-2 text-slate-700">Category</label>
                <select name="category_id" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50 focus:border-primary outline-none">
                    <?php if($categories): ?>
                        <?php foreach($categories['records'] as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php if($product['category_id'] == $c['id']) echo 'selected'; ?>>
                                <?php echo $c['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
             <div>
                <label class="block font-bold mb-2 text-slate-700">Price (IDR)</label>
                <input type="number" name="price" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50 focus:border-primary outline-none" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
        </div>

        <div>
            <label class="block font-bold mb-2 text-slate-700">Description</label>
            <textarea name="description" rows="5" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50 focus:border-primary outline-none"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t border-slate-100">
            <a href="index.php" class="px-6 py-2 rounded-lg text-slate-500 hover:bg-slate-50 font-bold transition">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-slate-900 text-white rounded-lg font-bold hover:bg-slate-800 transition shadow-lg">
                <?php echo $isEdit ? 'Update Product' : 'Create Product'; ?>
            </button>
        </div>
    </form>
</div>

</main>
</div>
</body>
</html>
