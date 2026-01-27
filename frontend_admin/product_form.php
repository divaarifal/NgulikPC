<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

$isEdit = false;
$product = [
    'name' => '', 'price' => '', 'description' => '', 'category_id' => '', 'brand' => '', 'slug' => ''
];

$categories = $api->get('/catalog/categories/read');

// Handle Edit Load
if(isset($_GET['id'])) {
    $isEdit = true;
    $slugInfo = $_GET['id']; // We passed slug in index.php
    $data = $api->get('/catalog/products/read_one?slug=' . $slugInfo);
    if($data && !isset($data['message'])) {
        $product = $data;
    }
}

// Handle Submit
$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [
        'name' => $_POST['name'],
        'price' => (int)$_POST['price'],
        'description' => $_POST['description'],
        'category_id' => (int)$_POST['category_id'],
        'brand' => $_POST['brand']
        // Handle images/specs later in more complex form
    ];

    if(isset($_POST['id']) && $_POST['id']) {
        // Update
        $payload['id'] = $_POST['id'];
        $res = $api->post('/catalog/products/update', $payload);
        if(isset($res['message']) && $res['message'] == "Product updated.") {
            $success = "Product updated successfully.";
            // Helper: refresh data
            $product = array_merge($product, $payload); 
        } else {
            $error = "Update failed.";
        }
    } else {
        // Create
        $res = $api->post('/catalog/products/create', $payload);
        if(isset($res['message']) && $res['message'] == "Product created.") {
            $success = "Product created successfully.";
            // Also init stock logic would be here
            header('Location: index.php'); // Back to list
            exit;
        } else {
            $error = "Creation failed. " . json_encode($res);
        }
    }
}

include '../frontend_main/includes/header.php';
?>

<div class="container mt-4 animate-up">
    <h1><?php echo $isEdit ? 'Edit Product' : 'Add New Product'; ?></h1>
    
    <div class="glass-panel" style="padding: 2rem; max-width: 800px; margin-top: 2rem;">
        <?php if($error) echo "<p style='color:red'>$error</p>"; ?>
        <?php if($success) echo "<p style='color:green'>$success</p>"; ?>

        <form method="POST">
            <?php if($isEdit): ?>
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <?php endif; ?>

            <label>Product Name</label>
            <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($product['name']); ?>" required>

            <label>Brand</label>
            <input type="text" name="brand" class="form-input" value="<?php echo htmlspecialchars($product['brand']); ?>" required>

            <label>Category</label>
            <select name="category_id" class="form-input" style="background:#0f0c29;">
                <?php if($categories): ?>
                    <?php foreach($categories['records'] as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php if($product['category_id'] == $c['id']) echo 'selected'; ?>>
                            <?php echo $c['name']; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label>Price</label>
            <input type="number" name="price" class="form-input" value="<?php echo htmlspecialchars($product['price']); ?>" required>

            <label>Description</label>
            <textarea name="description" class="form-input" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>

            <div class="flex" style="gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn"><?php echo $isEdit ? 'Update Product' : 'Create Product'; ?></button>
                <a href="index.php" class="btn" style="background: transparent; border: 1px solid var(--glass-border);">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../frontend_main/includes/footer.php'; ?>
