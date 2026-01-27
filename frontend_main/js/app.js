const API_URL = "http://localhost/NgulikPC/api_gateway";

async function fetchAPI(endpoint) {
    try {
        const response = await fetch(`${API_URL}${endpoint}`);
        return await response.json();
    } catch (error) {
        console.error("API Error:", error);
        return null;
    }
}

// Banners
async function loadBanners() {
    const data = await fetchAPI('/cms/banners/read');
    const container = document.getElementById('hero-slider');
    if (!container || !data || !data.records) return;

    data.records.forEach(banner => {
        const slide = document.createElement('div');
        slide.className = 'swiper-slide';
        // Placeholder check because banners in DB might be fake URLs
        const imgUrl = banner.image_url.startsWith('http') ? banner.image_url : 'assets/images/placeholder_banner.jpg';
        slide.innerHTML = `<img src="${imgUrl}" alt="${banner.title}">`;
        container.appendChild(slide);
    });
}

// Categories
async function loadCategories() {
    const data = await fetchAPI('/catalog/categories/read');
    const container = document.getElementById('categories-container');
    if (!container || !data || !data.records) return;

    data.records.forEach(cat => {
        const div = document.createElement('div');
        div.className = 'category-card';
        div.innerHTML = `
            <div class="category-icon"><i class="fas ${cat.icon}"></i></div>
            <h3>${cat.name}</h3>
        `;
        div.onclick = () => window.location.href = `products.html?category=${cat.slug}`;
        container.appendChild(div);
    });
}

// Products
async function loadProducts(filter = '') {
    const data = await fetchAPI(`/catalog/products/read${filter}`);
    const container = document.getElementById('products-container');
    if (!container) return;

    container.innerHTML = ''; // Clear

    if (!data || !data.records) {
        container.innerHTML = '<p>No products found.</p>';
        return;
    }

    data.records.forEach(product => {
        const div = document.createElement('div');
        div.className = 'product-card';
        // Handle images
        let img = 'assets/images/placeholder_gpu.png';
        if (product.images && product.images.length > 0) {
            // img = product.images[0]; 
        }

        const price = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(product.price);

        div.innerHTML = `
            <div class="product-img"></div> 
            <div class="product-info">
                <div class="product-category">${product.category_name}</div>
                <div class="product-title">${product.name}</div>
                <div class="product-price">${price}</div>
                <button class="btn btn-primary">Add to Cart</button>
            </div>
        `;
        div.onclick = (e) => {
            if (!e.target.classList.contains('btn'))
                window.location.href = `product-detail.html?slug=${product.slug}`;
        };
        container.appendChild(div);
    });
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    // Check which page we are on
    if (document.getElementById('hero-slider')) loadBanners();
    if (document.getElementById('categories-container')) loadCategories();
    if (document.getElementById('products-container')) loadProducts();
});
