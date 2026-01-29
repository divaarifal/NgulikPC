<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

$category_slug = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "?";
if($category_slug) $query .= "category=" . $category_slug . "&";
if($search) $query .= "search=" . $search;

$products = $api->get('/catalog/products/read' . $query);
$categories = $api->get('/catalog/categories/read');

// Extract Brands & Filter by Brand Logic
$brands = [];
$filtered_records = [];

if($products && isset($products['records'])) {
    foreach($products['records'] as $p) {
        if(isset($p['brand']) && !empty($p['brand'])) {
            $b = $p['brand'];
            if(!in_array($b, $brands)) $brands[] = $b;
        }
        
        // Filter logic
        if(isset($_GET['brand']) && !empty($_GET['brand'])) {
             if(isset($p['brand']) && $p['brand'] == $_GET['brand']) {
                 $filtered_records[] = $p;
             }
        } else {
            $filtered_records[] = $p;
        }
    }
    // Update products records to filtered list
    $products['records'] = $filtered_records;
}

include 'includes/header.php';
?>

<div class="container mx-auto px-6 py-12 flex flex-col lg:flex-row gap-12">
    <!-- Sidebar -->
    <aside class="w-full lg:w-64 flex-shrink-0">
        <form action="products.php" method="GET" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm sticky top-24">
            <h3 class="font-bold text-lg mb-4 text-slate-800">Search</h3>
            <input type="text" name="search" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-primary transition" placeholder="Keyword..." value="<?php echo htmlspecialchars($search); ?>">
            
            <h3 class="font-bold text-lg mt-8 mb-4 text-slate-800">Categories</h3>
            <ul class="space-y-2">
                <li>
                    <a href="products.php" class="block text-sm <?php echo ($category_slug == '') ? 'text-primary font-bold' : 'text-slate-500 hover:text-primary' ?> transition">
                        All Products
                    </a>
                </li>
                <?php if($categories && isset($categories['records'])): ?>
                    <?php foreach($categories['records'] as $cat): ?>
                        <li>
                            <a href="products.php?category=<?php echo $cat['slug']; ?>" class="block text-sm <?php echo ($category_slug == $cat['slug']) ? 'text-primary font-bold' : 'text-slate-500 hover:text-primary' ?> transition flex items-center">
                                <i class="fas <?php echo $cat['icon']; ?> w-6"></i> <?php echo $cat['name']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>

                <?php endif; ?>
            </ul>

            <h3 class="font-bold text-lg mt-8 mb-4 text-slate-800">Brands</h3>
             <ul class="space-y-2">
                <li>
                    <a href="products.php" class="block text-sm <?php echo (!isset($_GET['brand']) || $_GET['brand'] == '') ? 'text-primary font-bold' : 'text-slate-500 hover:text-primary' ?> transition">
                        All Brands
                    </a>
                </li>
                <?php foreach($brands as $b): ?>
                    <li>
                         <a href="products.php?brand=<?php echo urlencode($b); ?><?php echo $category_slug ? '&category='.$category_slug : ''; ?>" class="block text-sm <?php echo (isset($_GET['brand']) && $_GET['brand'] == $b) ? 'text-primary font-bold' : 'text-slate-500 hover:text-primary' ?> transition">
                            <?php echo $b; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
             </ul>
        </form>
    </aside>

    <!-- Content -->
    <div class="flex-1">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">Shop Products</h1>
            <span class="text-slate-400 text-sm"><?php echo isset($products['records']) ? count($products['records']) : 0; ?> Items</span>
        </div>

        <?php if($products && isset($products['records']) && count($products['records']) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                <?php foreach($products['records'] as $p): ?>
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl transition group overflow-hidden relative">
                        <!-- Image -->
                        <div class="h-64 bg-slate-50 relative overflow-hidden">
                            <img src="<?php echo (isset($p['images'][0]) ? $p['images'][0] : 'assets/images/placeholder_gpu.png'); ?>" alt="<?php echo $p['name']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            
                            <!-- Overlay Button -->
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <button onclick="openModal(<?php echo htmlspecialchars(json_encode($p)); ?>)" class="bg-white text-slate-800 px-6 py-2 rounded-full font-bold shadow-lg hover:bg-primary hover:text-white transition transform translate-y-4 group-hover:translate-y-0">
                                    Quick View
                                </button>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-xs font-bold text-primary uppercase tracking-wider"><?php echo $p['category_name']; ?></div>
                                <?php if(isset($p['brand'])): ?>
                                    <span class="text-xs text-slate-400 font-bold uppercase"><?php echo $p['brand']; ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="font-bold text-lg text-slate-800 mb-2 truncate"><?php echo $p['name']; ?></h3>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-slate-800">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></span>
                                <button onclick="openModal(<?php echo htmlspecialchars(json_encode($p)); ?>)" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-primary hover:text-white transition">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20 bg-white rounded-2xl border border-slate-100">
                <i class="fas fa-box-open text-4xl text-slate-300 mb-4"></i>
                <p class="text-slate-500">No products found for your criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div id="productModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <!-- Modal Content -->
    <div class="relative bg-white w-full max-w-4xl mx-auto mt-20 rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row animate-up">
        <button onclick="closeModal()" class="absolute top-4 right-4 z-10 w-10 h-10 bg-white/80 rounded-full flex items-center justify-center text-slate-500 hover:text-red-500 transition">
            <i class="fas fa-times"></i>
        </button>

        <!-- Image Side -->
        <div class="w-full md:w-1/2 bg-slate-50 p-8 flex items-center justify-center">
             <img id="modalImage" src="" class="max-w-full max-h-[400px] object-contain drop-shadow-lg">
        </div>

        <!-- Info Side -->
        <div class="w-full md:w-1/2 p-8 md:p-12">
            <span id="modalCategory" class="text-xs font-bold text-primary uppercase tracking-wider"></span>
            <h2 id="modalName" class="text-3xl font-bold text-slate-900 mt-2 mb-4 leading-tight"></h2>
            <div id="modalPrice" class="text-2xl font-bold text-slate-800 mb-6"></div>
            
            <p id="modalDesc" class="text-slate-500 leading-relaxed mb-6 text-sm line-clamp-4"></p>

            <div class="mb-4">
                <span class="text-sm font-bold text-slate-700">Stock: </span>
                <span id="modalStock" class="text-sm text-slate-500">Checking...</span>
            </div>

            <form action="cart.php" method="POST" class="flex gap-4">
                <input type="hidden" name="action" value="add">
                <input type="hidden" id="modalId" name="product_id">
                <input type="hidden" id="modalNameInput" name="name">
                <input type="hidden" id="modalPriceInput" name="price">
                
                <input type="number" id="modalQty" name="quantity" value="1" min="1" class="w-20 bg-slate-50 border border-slate-200 rounded-lg text-center font-bold focus:outline-none focus:border-primary">
                
                <button type="submit" id="modalSubmit" class="flex-1 bg-primary text-white font-bold py-3 rounded-xl shadow-lg shadow-red-500/30 hover:bg-red-600 transition flex items-center justify-center gap-2">
                    <i class="fas fa-shopping-cart"></i> Buy Now
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(product) {
        document.getElementById('productModal').classList.remove('hidden');
        document.getElementById('modalImage').src = (product.images && product.images[0]) ? product.images[0] : 'assets/images/placeholder_gpu.png';
        document.getElementById('modalName').textContent = product.name;
        document.getElementById('modalPrice').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(product.price);
        document.getElementById('modalCategory').textContent = product.category_name || '';
        document.getElementById('modalDesc').textContent = product.description;
        
        // Form Inputs
        document.getElementById('modalId').value = product.id;
        document.getElementById('modalNameInput').value = product.name;
        document.getElementById('modalPriceInput').value = product.price;

        // Reset and Fetch Stock
        const stockEl = document.getElementById('modalStock');
        const submitBtn = document.getElementById('modalSubmit');
        const qtyInput = document.getElementById('modalQty');
        
        stockEl.textContent = 'Checking...';
        stockEl.className = 'text-sm text-slate-500';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

        // Use relative path which is safer for standard directory structure
        fetch('../api_gateway/index.php/inventory/stock/read?product_id=' + product.id)
            .then(response => {
                if(!response.ok) {
                    console.error("Stock API Error:", response.status);
                    return {available: 0}; 
                }
                return response.json();
            })
            .then(data => {
                // Ensure available is a number
                let qty = (data && typeof data.available !== 'undefined') ? parseInt(data.available) : 0;
                
                stockEl.textContent = qty + ' items available';
                
                if(qty > 0) {
                    stockEl.className = 'text-sm text-green-600 font-bold';
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    qtyInput.max = qty;
                } else {
                    stockEl.textContent = 'Out of Stock (0 items)';
                    stockEl.className = 'text-sm text-red-600 font-bold';
                    // Keep disabled
                }
            })
            .catch(err => {
                console.error("Stock check failed:", err);
                stockEl.textContent = 'Stock Check Failed';
                // Fail safe: enable or disable? User wants to see stock. 
                // If check fails, maybe allow default order (backorder)? 
                // Or stick to disabled to prevent errors.
                // Let's stick to showing error text.
            });
    }

    function closeModal() {
        document.getElementById('productModal').classList.add('hidden');
    }
</script>

<?php include 'includes/footer.php'; ?>
