<?php
session_start();
include '../frontend_main/includes/api_client.php';
$api = new ApiClient();

$products = $api->get('/catalog/products/read');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse - NgulikPC</title>
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
<body class="bg-slate-100 p-8">

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-12">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Warehouse Management</h1>
            <p class="text-slate-500">Real-time stock monitoring</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-lg shadow-sm font-bold text-slate-700">
            <i class="fas fa-box text-primary mr-2"></i> Total Items: <?php echo isset($products['records']) ? count($products['records']) : 0; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php if($products && isset($products['records'])): ?>
            <?php foreach($products['records'] as $p): ?>
                <?php 
                    $stock = $api->get('/inventory/stock/read?product_id=' . $p['id']);
                    $qty = ($stock && isset($stock['quantity'])) ? $stock['quantity'] : 0;
                    $reserved = ($stock && isset($stock['reserved'])) ? $stock['reserved'] : 0;
                    $isLow = $qty < 5;
                ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm border <?php echo $isLow ? 'border-red-200' : 'border-slate-100'; ?>">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-xs font-bold uppercase text-slate-400">ID: #<?php echo $p['id']; ?></span>
                        <?php if($isLow): ?>
                            <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-md">Low Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="font-bold text-slate-800 text-lg mb-4 truncate" title="<?php echo $p['name']; ?>"><?php echo $p['name']; ?></h3>
                    
                    <div class="flex gap-4 mb-6">
                        <div class="flex-1 bg-slate-50 p-3 rounded-xl text-center">
                            <div class="text-slate-500 text-xs uppercase font-bold">Total</div>
                            <div class="text-2xl font-bold text-slate-800"><?php echo $qty; ?></div>
                        </div>
                        <div class="flex-1 bg-slate-50 p-3 rounded-xl text-center">
                            <div class="text-slate-500 text-xs uppercase font-bold">Reserved</div>
                            <div class="text-2xl font-bold text-blue-500"><?php echo $reserved; ?></div>
                        </div>
                    </div>

                    <form action="stock_update.php" method="POST" class="flex gap-2">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <input type="number" name="quantity" value="<?php echo $qty; ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-center font-bold focus:border-primary outline-none">
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-red-600 transition"><i class="fas fa-save"></i></button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
