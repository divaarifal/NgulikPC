<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

$error = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $api->post('/auth/login', ['email' => $email, 'password' => $password]);

    if(isset($result['token'])) {
        $_SESSION['token'] = $result['token'];
        $_SESSION['user'] = $result['user'];
        
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
        header("Location: $redirect");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NgulikPC</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center;">
    <div class="glass-panel animate-up" style="padding: 3rem; width: 100%; max-width: 400px;">
        <h2 class="text-center" style="margin-bottom: 2rem;">Login</h2>
        
        <?php if($error): ?>
            <div style="background: rgba(245,56,68,0.2); color: #ff6b6b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" class="form-input" placeholder="Email" required>
            <input type="password" name="password" class="form-input" placeholder="Password" required>
            <button type="submit" class="btn" style="width: 100%;">Sign In</button>
        </form>
        
        <p class="text-center" style="margin-top: 1rem; color: var(--text-muted);">
            Don't have an account? <a href="register.php" style="color: var(--primary);">Register</a>
        </p>
        <p class="text-center" style="margin-top: 1rem;">
            <a href="index.php" style="color: var(--text-muted); font-size: 0.9rem;">Back to Home</a>
        </p>
    </div>
</body>
</html>
