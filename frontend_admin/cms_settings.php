<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

$message = "";

// Handle Update
// Handle Update
// Handle Update
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Handle File Uploads Locally
    $bannerPaths = [];
    $targetDir = "../frontend_main/assets/uploads/banners/";
    if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    for($i=1; $i<=3; $i++) {
        $inputName = 'banner_image_' . $i;
        $keyName = 'hero_banner_' . $i;
        
        if(isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == 0) {
            $fileExt = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
            $fileName = "banner_" . $i . "_" . time() . "." . $fileExt;
            $targetFile = $targetDir . $fileName;
            
            if(move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetFile)) {
                // Success: Path relative to frontend_main
                $bannerPaths[$keyName] = "assets/uploads/banners/" . $fileName;
            }
        }
    }

    // 2. Prepare Settings Array
    $settings = [
        'site_title' => $_POST['site_title'],
        'header_text' => $_POST['header_text'],
        'footer_text' => $_POST['footer_text']
    ];
    
    // Merge new banner paths
    foreach($bannerPaths as $k => $v) {
        $settings[$k] = $v;
    }

    // 3. Call Update API (Text Data is fine via API)
    // We can still use the API for the text updates to keep it somewhat clean, 
    // or we could use direct DB. API is fine for small text payloads.
    $res = $api->post('/cms/settings/update', $settings); 
    
    if(isset($res['message']) && $res['message'] == "Settings updated.") {
        $message = "Settings updated successfully.";
    } else {
        $message = "Failed to update settings. API Response: " . json_encode($res);
    }
}

// Fetch Settings
$settings_data = $api->get('/cms/settings/read'); 

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
        
        <h3 class="font-bold text-lg mb-4 text-slate-700 border-b pb-2">Home Banner Slideshow</h3>
        <div class="grid grid-cols-1 gap-6 mb-6">
            <?php for($i=1; $i<=3; $i++): ?>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <label class="block font-bold mb-2 text-sm text-slate-600">Slide #<?php echo $i; ?></label>
                <?php if(isset($s['hero_banner_'.$i]) && !empty($s['hero_banner_'.$i])): ?>
                    <img src="../frontend_main/<?php echo $s['hero_banner_'.$i]; ?>" class="h-24 w-full object-cover rounded-lg mb-2 bg-white">
                <?php endif; ?>
                <input type="file" name="banner_image_<?php echo $i; ?>" class="w-full text-sm">
            </div>
            <?php endfor; ?>
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
