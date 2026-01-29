<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NgulikPC - Ultimate Hardware Store</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ef4444', // Red-500
                        secondary: '#3b82f6', // Blue-500
                        dark: '#1e293b', // Slate-800
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <header class="glass sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary">
                <i class="fas fa-microchip text-primary mr-2"></i>NgulikPC
            </a>
            
            <nav class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-slate-600 hover:text-primary font-medium transition">Home</a>
                <a href="products.php" class="text-slate-600 hover:text-primary font-medium transition">Store</a>
                <a href="cart.php" class="text-slate-600 hover:text-primary font-medium transition relative">
                    <i class="fas fa-shopping-cart text-lg"></i>
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                           <?php echo count($_SESSION['cart']); ?>
                        </span>
                    <?php endif; ?>
                </a>

                <?php if(isset($_SESSION['user'])): ?>
                    <div class="relative">
                         <button onclick="toggleDropdown()" id="userMenuBtn" class="flex items-center space-x-2 bg-white border border-slate-200 px-4 py-2 rounded-full shadow-sm hover:shadow-md transition">
                            <img src="<?php echo isset($_SESSION['user']['avatar']) ? $_SESSION['user']['avatar'] : 'assets/images/default_avatar.png'; ?>" class="w-8 h-8 rounded-full object-cover">
                            <span class="font-medium text-sm">Hi, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
                            <i class="fas fa-chevron-down text-xs text-slate-400 Transition-transform duration-200" id="dropdownIcon"></i>
                         </button>
                         <div id="userDropdown" class="absolute hidden right-0 mt-2 w-48 bg-white border border-slate-100 rounded-xl shadow-lg py-2 z-50 animate-fade-in-down">
                             <a href="order_history.php" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary"><i class="fas fa-shopping-bag mr-2"></i>My Orders</a>
                             <a href="profile.php" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary"><i class="fas fa-user-cog mr-2"></i>Settings</a>
                             <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                         </div>
                    </div>
                    <script>
                        function toggleDropdown() {
                            const dropdown = document.getElementById('userDropdown');
                            const icon = document.getElementById('dropdownIcon');
                            dropdown.classList.toggle('hidden');
                            if(!dropdown.classList.contains('hidden')) {
                                icon.style.transform = 'rotate(180deg)';
                            } else {
                                icon.style.transform = 'rotate(0deg)';
                            }
                        }

                        // Close when clicking outside
                        window.onclick = function(event) {
                            if (!event.target.closest('#userMenuBtn')) {
                                const dropdown = document.getElementById('userDropdown');
                                const icon = document.getElementById('dropdownIcon');
                                if (!dropdown.classList.contains('hidden')) {
                                    dropdown.classList.add('hidden');
                                    icon.style.transform = 'rotate(0deg)';
                                }
                            }
                        }
                    </script>
                <?php else: ?>
                    <a href="login.php" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-red-600 shadow-lg shadow-red-500/30 transition text-sm font-bold">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="flex-grow">
