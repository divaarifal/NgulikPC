<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - NgulikPC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#ef4444', secondary: '#3b82f6', dark: '#1e293b' },
                    fontFamily: { sans: ['Outfit', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white flex-shrink-0">
            <div class="p-6">
                <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary">NgulikPC Admin</h1>
            </div>
            <nav class="mt-6">
                <a href="index.php" class="block px-6 py-3 hover:bg-slate-800 transition"><i class="fas fa-chart-line w-6"></i> Dashboard</a>
                <a href="products.php" class="block px-6 py-3 hover:bg-slate-800 transition"><i class="fas fa-box-open w-6"></i> Product Catalog</a>
                <a href="cms_settings.php" class="block px-6 py-3 hover:bg-slate-800 transition"><i class="fas fa-edit w-6"></i> CMS / Content</a>
                <a href="categories.php" class="block px-6 py-3 hover:bg-slate-800 transition"><i class="fas fa-tags w-6"></i> Categories</a>
                <a href="orders.php" class="block px-6 py-3 hover:bg-slate-800 transition"><i class="fas fa-shopping-cart w-6"></i> Orders</a>
                <a href="../frontend_main/index.php" class="block px-6 py-3 hover:bg-slate-800 transition text-slate-400 mt-8"><i class="fas fa-external-link-alt w-6"></i> Visit Site</a>
            </nav>
        </aside>

        <!-- Main -->
        <main class="flex-1 p-8">
