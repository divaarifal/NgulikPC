-- Database: Auth Service
CREATE DATABASE IF NOT EXISTS ngulikpc_auth;
USE ngulikpc_auth;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'warehouse') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token TEXT NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Database: Catalog Service
CREATE DATABASE IF NOT EXISTS ngulikpc_catalog;
USE ngulikpc_catalog;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(100) -- Path to icon or class name
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    brand VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    images JSON, -- Store array of image URLs
    specs JSON, -- Store flexible specs (socket, dimensions, etc.)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Database: Inventory Service
CREATE DATABASE IF NOT EXISTS ngulikpc_inventory;
USE ngulikpc_inventory;

CREATE TABLE IF NOT EXISTS stocks (
    product_id INT PRIMARY KEY,
    quantity INT DEFAULT 0,
    reserved INT DEFAULT 0, -- For items currently in checkout process
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Database: Order Service
CREATE DATABASE IF NOT EXISTS ngulikpc_order;
USE ngulikpc_order;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'cancelled') DEFAULT 'pending',
    total_price DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200), -- Snapshot of name at purchase
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL, -- Snapshot of price at purchase
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Database: CMS Service
CREATE DATABASE IF NOT EXISTS ngulikpc_cms;
USE ngulikpc_cms;

CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    image_url VARCHAR(255) NOT NULL,
    link_url VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    content TEXT,
    author_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
