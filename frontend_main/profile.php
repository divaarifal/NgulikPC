<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

if(!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

$user = $_SESSION['user'];
$success = "";
$error = "";

// Handle Address Add
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_address') {
    $payload = [
        'user_id' => $user['id'],
        'label' => $_POST['label'],
        'recipient_name' => $_POST['recipient_name'],
        'phone_number' => $_POST['phone_number'],
        'address_line' => $_POST['address_line']
    ];
    $res = $api->post('/auth/addresses/create', $payload);
    if(isset($res['message']) && $res['message'] == "Address added.") {
        $success = "Address added successfully!";
    } else {
        $error = "Failed to add address.";
    }
}

// Handle Profile Update (Info / Avatar)
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    // Handle Avatar Upload
    if(isset($_FILES['avatar_image']) && $_FILES['avatar_image']['error'] == 0) {
         $file = new CURLFile($_FILES['avatar_image']['tmp_name'], $_FILES['avatar_image']['type'], $_FILES['avatar_image']['name']);
         $up_res = $api->post('/auth/user/upload_avatar', ['avatar_image' => $file, 'user_id' => $user['id']], null, true);
         
         if(isset($up_res['path'])) {
             // Update SessionUser
             $_SESSION['user']['avatar'] = $up_res['path'];
             $user['avatar'] = $up_res['path']; // Update local var for display
             $success = "Avatar updated. ";
         } else {
             $error = "Avatar upload failed: " . (isset($up_res['message']) ? $up_res['message'] : 'Unknown error');
         }
    } elseif(isset($_FILES['avatar_image']) && $_FILES['avatar_image']['error'] != 0) {
        $error = "File upload error code: " . $_FILES['avatar_image']['error'];
    }

    // In real app, call /auth/update API for other fields
    // $_SESSION['user']['username'] = $_POST['username'];
    // $success .= "Profile info updated.";
}

// Fetch Addresses
$addresses = $api->get('/auth/addresses/read?user_id=' . $user['id']);

include 'includes/header.php';
?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold text-slate-800 mb-8">My Account</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Profile Sidebar -->
        <div class="w-full lg:w-1/3">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-center">
                <div class="relative inline-block">
                    <img src="<?php echo isset($user['avatar']) ? $user['avatar'] : 'assets/images/default_avatar.png'; ?>" class="w-32 h-32 rounded-full object-cover border-4 border-slate-50 mb-4">
                    <button type="button" onclick="document.getElementById('avatarInput').click()" class="absolute bottom-4 right-0 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center hover:bg-red-600 transition"><i class="fas fa-camera"></i></button>
                </div>
                <h2 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($user['username']); ?></h2>
                <p class="text-slate-500 text-sm"><?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?></p>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm mt-6">
                <h3 class="font-bold text-lg mb-4">Account Details</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <!-- Avatar Upload (Hidden Input Triggered by JS) -->
                    <input type="file" name="avatar_image" id="avatarInput" class="hidden" onchange="this.form.submit()">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-sm" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                            <input type="password" name="password" placeholder="Change Password" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-sm">
                        </div>
                        <button type="submit" class="w-full bg-slate-800 text-white py-2 rounded-lg font-medium hover:bg-slate-900 transition">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Address Book -->
        <div class="w-full lg:w-2/3">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg">Address Book</h3>
                    <button onclick="document.getElementById('addAddressForm').classList.toggle('hidden')" class="text-primary text-sm font-bold hover:underline">+ Add New Address</button>
                </div>

                <?php if($success): ?>
                    <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-sm"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if($error): ?>
                    <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Add Form -->
                <form id="addAddressForm" method="POST" class="hidden mb-8 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <input type="hidden" name="action" value="add_address">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <input type="text" name="label" placeholder="Label (e.g. Home)" class="w-full border border-slate-200 rounded-lg px-4 py-2" required>
                        <input type="text" name="recipient_name" placeholder="Recipient Name" class="w-full border border-slate-200 rounded-lg px-4 py-2" required>
                        <input type="text" name="phone_number" placeholder="Phone Number" class="w-full border border-slate-200 rounded-lg px-4 py-2" required>
                    </div>
                    <textarea name="address_line" placeholder="Full Address..." class="w-full border border-slate-200 rounded-lg px-4 py-2 mb-4" rows="3" required></textarea>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('addAddressForm').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:bg-slate-200 rounded-lg transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-red-600 transition">Save Address</button>
                    </div>
                </form>
                
                <!-- List -->
                <div class="space-y-4">
                    <?php if($addresses && isset($addresses['records']) && count($addresses['records']) > 0): ?>
                        <?php foreach($addresses['records'] as $addr): ?>
                            <div class="border border-slate-200 rounded-xl p-4 flex justify-between items-start hover:border-primary transition">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-slate-800"><?php echo $addr['label']; ?></span>
                                        <?php if(isset($addr['is_primary']) && $addr['is_primary']): ?>
                                            <span class="bg-blue-100 text-blue-600 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full">Primary</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm text-slate-600 font-medium"><?php echo $addr['recipient_name']; ?> | <?php echo $addr['phone_number']; ?></p>
                                    <p class="text-sm text-slate-500 mt-1"><?php echo $addr['address_line']; ?></p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="text-slate-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                                    <button class="text-slate-400 hover:text-red-500"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-slate-500 italic">No addresses saved yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
