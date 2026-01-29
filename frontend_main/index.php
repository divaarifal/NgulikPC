<?php
session_start();
include 'includes/api_client.php';
$api = new ApiClient();

$banners = $api->get('/cms/banners/read');
$categories = $api->get('/catalog/categories/read');
$newArrivals = $api->get('/catalog/products/read');

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative bg-white overflow-hidden">
    <div class="absolute inset-0 bg-slate-50/50"></div>
    <div class="container mx-auto px-6 py-20 relative z-10 flex flex-col md:flex-row items-center">
        <div class="w-full md:w-1/2 mb-10 md:mb-0">
            <span class="inline-block py-1 px-3 rounded-full bg-blue-100 text-blue-600 text-xs font-bold uppercase tracking-wide mb-4">New Season</span>
            <h1 class="text-5xl md:text-6xl font-bold text-slate-900 leading-tight mb-6">
                Build Your <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Dream Machine</span>
            </h1>
            <p class="text-lg text-slate-500 mb-8 max-w-lg">Premium components for high-performance gaming and professional workstations. Experience power like never before.</p>
            <div class="flex gap-4">
                <a href="products.php" class="px-8 py-3 bg-primary text-white rounded-full font-bold shadow-lg shadow-red-500/30 hover:bg-red-600 transition">Shop Now</a>
                <a href="#featured" class="px-8 py-3 bg-white text-slate-700 border border-slate-200 rounded-full font-bold hover:bg-slate-50 transition">Explore</a>
            </div>
        </div>
        <div class="w-full md:w-1/2 relative">
            <!-- Carousel -->
             <?php
                // Fetch Settings specifically for banners
                // We'll reuse the $banners variable but populated from settings
                // Or better, just fetch settings.
                $settingsData = $api->get('/cms/settings/read');
                $heroBanners = [];
                if($settingsData && isset($settingsData['records'])) {
                    foreach($settingsData['records'] as $row) {
                        if(strpos($row['setting_key'], 'hero_banner_') !== false) {
                           $heroBanners[] = $row['setting_value'];
                        }
                    }
                }
                // Sort or ensure order? keys are hero_banner_1, 2, 3. 
                // The loop order depends on DB. We can trust array order or sort if strict.
                // For now simple display is fine.
             ?>
             
             <?php if(count($heroBanners) > 0): ?>
                <div class="relative rounded-3xl overflow-hidden shadow-2xl h-[400px] group" id="heroCarousel">
                    <div class="flex transition-transform duration-700 ease-in-out h-full" id="carouselSlides">
                        <?php foreach($heroBanners as $bannerPath): ?>
                            <div class="w-full h-full flex-shrink-0 relative">
                                <img src="<?php echo $bannerPath; ?>" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Controls -->
                    <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/30 backdrop-blur text-white p-3 rounded-full hover:bg-white hover:text-primary transition opacity-0 group-hover:opacity-100">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/30 backdrop-blur text-white p-3 rounded-full hover:bg-white hover:text-primary transition opacity-0 group-hover:opacity-100">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    
                    <!-- Indicators -->
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                        <?php foreach($heroBanners as $index => $b): ?>
                            <button onclick="goToSlide(<?php echo $index; ?>)" class="w-2 h-2 rounded-full bg-white/50 hover:bg-white transition indicator" data-index="<?php echo $index; ?>"></button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <script>
                    let currentSlide = 0;
                    const slides = document.getElementById('carouselSlides');
                    const indicators = document.querySelectorAll('.indicator');
                    const totalSlides = <?php echo count($heroBanners); ?>;
                    
                    function updateCarousel() {
                        slides.style.transform = `translateX(-${currentSlide * 100}%)`;
                        indicators.forEach((ind, i) => {
                            if(i === currentSlide) {
                                ind.classList.remove('bg-white/50');
                                ind.classList.add('bg-white', 'scale-125');
                            } else {
                                ind.classList.add('bg-white/50');
                                ind.classList.remove('bg-white', 'scale-125');
                            }
                        });
                    }
                    
                    function nextSlide() {
                        currentSlide = (currentSlide + 1) % totalSlides;
                        updateCarousel();
                    }
                    
                    function prevSlide() {
                        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                        updateCarousel();
                    }
                    
                    function goToSlide(index) {
                        currentSlide = index;
                        updateCarousel();
                    }
                    
                    // Auto Play
                    setInterval(nextSlide, 5000);
                    updateCarousel(); // Init
                </script>
             <?php else: ?>
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl h-[400px] flex items-center justify-center text-white shadow-2xl">
                    <h2 class="text-2xl font-bold">Latest Tech Arrived</h2>
                </div>
             <?php endif; ?>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-20 bg-slate-50">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-slate-800 text-center mb-12">Browse by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php if($categories && isset($categories['records'])): ?>
                <?php foreach($categories['records'] as $cat): ?>
                    <a href="products.php?category=<?php echo $cat['slug']; ?>" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center justify-center hover:shadow-lg hover:-translate-y-1 transition duration-300 group">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4 group-hover:bg-primary group-hover:text-white transition text-secondary">
                            <i class="fas <?php echo $cat['icon']; ?> text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-slate-700 group-hover:text-primary transition"><?php echo $cat['name']; ?></h3>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured -->
<section id="featured" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">New Arrivals</h2>
                <p class="text-slate-500 mt-2">Check out the latest drops.</p>
            </div>
            <a href="products.php" class="text-primary font-bold hover:text-red-700 transition">View All <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
             <?php if($newArrivals && isset($newArrivals['records'])): ?>
                <?php 
                    $count = 0;
                    foreach($newArrivals['records'] as $p): 
                    if($count >= 4) break;
                    $count++;
                ?>
                <div class="group">
                    <div class="bg-slate-50 rounded-2xl p-4 mb-4 relative overflow-hidden">
                        <img src="<?php echo (isset($p['images'][0]) ? $p['images'][0] : 'assets/images/placeholder_gpu.png'); ?>" class="w-full h-48 object-contain mix-blend-multiply group-hover:scale-110 transition duration-500">
                    </div>
                    <div class="px-2">
                        <h3 class="font-bold text-slate-800 truncate"><?php echo $p['name']; ?></h3>
                        <p class="text-slate-500 text-sm mb-2"><?php echo $p['category_name']; ?></p>
                        <div class="flex justify-between items-center bg-white">
                             <span class="font-bold text-primary text-lg">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
