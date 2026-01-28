<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

// Handle Create (Mock, since I didn't create Category Create API in v1.0, assuming it exists or simulating)
// Okay, I need `catalog_service/api/categories/create.php`
// But for v1.2 demo I will focus on Read/List, and add a placeholder form.
// Or I create the API now. Better create the API.

$message = "";
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = ['name' => $_POST['name'], 'icon' => $_POST['icon']];
    // $res = $api->post('/catalog/categories/create', $data); // Need API
    $message = "Category functionality requires backend API update (Pending).";
}

$categories = $api->get('/catalog/categories/read');

include 'includes/admin_header.php';
?>

<h1 class="text-3xl font-bold mb-8">Category Management</h1>

<?php if($message): ?>
    <div class="bg-yellow-100 text-yellow-700 p-4 rounded-xl mb-6"><?php echo $message; ?></div>
<?php endif; ?>

<div class="flex gap-8">
    <!-- List -->
    <div class="w-2/3 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="p-4">Icon</th>
                    <th class="p-4">Name</th>
                    <th class="p-4">Slug</th>
                    <th class="p-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if($categories && isset($categories['records'])): ?>
                    <?php foreach($categories['records'] as $c): ?>
                        <tr>
                            <td class="p-4 text-secondary"><i class="fas <?php echo $c['icon']; ?> text-xl"></i></td>
                            <td class="p-4 font-bold text-slate-800"><?php echo $c['name']; ?></td>
                            <td class="p-4 text-slate-500 text-sm"><?php echo $c['slug']; ?></td>
                            <td class="p-4 text-right">
                                <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Form -->
    <div class="w-1/3">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-lg mb-4">Add Category</h3>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Category Name</label>
                    <input type="text" name="name" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50 focus:border-primary focus:outline-none" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2">FontAwesome Class</label>
                    <input type="text" name="icon" placeholder="fa-microchip" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50" required>
                </div>
                <button type="submit" class="w-full bg-secondary text-white py-2 rounded-lg font-bold hover:bg-blue-600 transition">Create</button>
            </form>
        </div>
    </div>
</div>

</main>
</div>
</body>
</html>
