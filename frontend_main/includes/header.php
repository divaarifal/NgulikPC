<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NgulikPC - Ultimate Hardware Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container flex justify-between items-center">
            <a href="index.php" class="logo">
                <i class="fas fa-microchip"></i> NgulikPC
            </a>
            <nav>
                <a href="index.php" class="nav-link">Home</a>
                <a href="products.php" class="nav-link">Catalog</a>
                <a href="#" class="nav-link">Build PC</a>
                <a href="cart.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Cart</a>
                <?php if(isset($_SESSION['user'])): ?>
                    <a href="logout.php" class="nav-link btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main>
