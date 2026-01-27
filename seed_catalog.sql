USE ngulikpc_catalog;

INSERT INTO categories (name, slug, icon) VALUES 
('Processor', 'processor', 'fa-microchip'),
('RAM', 'ram', 'fa-memory'),
('GPU', 'gpu', 'fa-video'),
('Motherboard', 'motherboard', 'fa-server'),
('Storage', 'storage', 'fa-hdd'),
('Power Supply', 'power-supply', 'fa-plug');

INSERT INTO products (category_id, name, slug, brand, price, description, images, specs) VALUES 
(1, 'Intel Core i9-14900K', 'intel-core-i9-14900k', 'Intel', 9500000, 'Processor desktop Intel Core i9-14900K Generasi ke-14. Dengan 24 Core dan 32 Thread.', '["https://example.com/i9-14900k-1.jpg", "https://example.com/i9-14900k-2.jpg"]', '{"socket": "LGA1700", "base_clock": "3.2 GHz", "boost_clock": "6.0 GHz", "wattage": "125W"}'),
(3, 'ASUS ROG Strix GeForce RTX 4090', 'asus-rog-strix-rtx-4090', 'ASUS', 32000000, 'Kartu grafis gaming terbaik dengan performa monster.', '["https://example.com/rtx4090-1.jpg"]', '{"vram": "24GB GDDR6X", "cuda_cores": 16384, "boost_clock": "2640 MHz"}'),
(2, 'Corsair Dominator Titanium 32GB DDR5', 'corsair-dominator-titanium-32gb', 'Corsair', 3500000, 'RAM DDR5 premium dengan performa tinggi dan RGB yang memukau.', '["https://example.com/ram-corsair.jpg"]', '{"speed": "6000MHz", "latency": "CL30", "capacity": "2x16GB"}');
