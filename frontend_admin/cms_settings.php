<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

$message = "";

// Handle Update
// Handle Update
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_title' => $_POST['site_title'],
        'header_text' => $_POST['header_text'],
        'footer_text' => $_POST['footer_text']
    ];

    // Handle File Upload
    if(isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        $file = new CURLFile($_FILES['banner_image']['tmp_name'], $_FILES['banner_image']['type'], $_FILES['banner_image']['name']);
        $upload_res = $api->post('/cms/banners/upload', ['banner_image' => $file], null, true);
        
        if(isset($upload_res['path'])) {
            $settings['hero_banner'] = $upload_res['path'];
        }
    }
    
    $res = $api->post('/cms/settings/update', $settings); 
    if(isset($res['message']) && $res['message'] == "Settings updated.") {
        $message = "Settings updated successfully.";
    } else {
        $message = "Failed to update settings.";
    }
}

// Fetch Settings
$settings_data = $api->get('/cms/settings/read'); // Need to create this API

// Pre-fill
$s = [];
if($settings_data && isset($settings_data['records'])) {
    foreach($settings_data['records'] as $row) {
        $s[$row['setting_key']] = $row['setting_value'];
    }
}

include 'includes/admin_header.php';
?>

<h1 class="text-3xl font-bold mb-8">Content Management</h1>

<?php if($message): ?>
    <div class="bg-blue-100 text-blue-700 p-4 rounded-xl mb-6"><?php echo $message; ?></div>
<?php endif; ?>

<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 max-w-2xl">
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block font-bold mb-2">Banner Image</label>
            <?php if(isset($s['hero_banner'])): ?>
                <img src="../frontend_main/<?php echo $s['hero_banner']; ?>" class="h-32 rounded-lg mb-2 object-cover">
            <?php endif; ?>
            <input type="file" name="banner_image" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50">
        </div>
        
        <div class="mb-4">
            <label class="block font-bold mb-2">Website Title</label>
            <input type="text" name="site_title" value="<?php echo isset($s['site_title']) ? htmlspecialchars($s['site_title']) : ''; ?>" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-2">Header Text</label>
            <input type="text" name="header_text" value="<?php echo isset($s['header_text']) ? htmlspecialchars($s['header_text']) : ''; ?>" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50">
        </div>
         <div class="mb-6">
            <label class="block font-bold mb-2">Footer Text</label>
            <input type="text" name="footer_text" value="<?php echo isset($s['footer_text']) ? htmlspecialchars($s['footer_text']) : ''; ?>" class="w-full border border-slate-200 rounded-lg px-4 py-2 bg-slate-50">
        </div>
        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-red-600 transition">Save Changes</button>
    </form>
</div>

</main>
</div>
</body>
</html>
