<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $api->post('/auth/register', [
        'username' => $username,
        'email' => $email,
        'password' => $password
    ]);

    if(isset($result['message']) && $result['message'] == "User was created.") {
        $success = "Registration successful! You can now login.";
    } else {
        $error = "Registration failed. Email might really be in use.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NgulikPC</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center;">
    <div class="glass-panel animate-up" style="padding: 3rem; width: 100%; max-width: 400px;">
        <h2 class="text-center" style="margin-bottom: 2rem;">Create Account</h2>
        
        <?php if($error): ?>
            <div style="background: rgba(245,56,68,0.2); color: #ff6b6b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div style="background: rgba(0, 242, 96, 0.2); color: #00f260; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" class="form-input" placeholder="Username" required>
            <input type="email" name="email" class="form-input" placeholder="Email" required>
            <input type="password" name="password" class="form-input" placeholder="Password" required>
            <button type="submit" class="btn" style="width: 100%;">Sign Up</button>
        </form>
        
        <p class="text-center" style="margin-top: 1rem; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--primary);">Login</a>
        </p>
    </div>
</body>
</html>
