-- Database: Auth Service Updates
USE ngulikpc_auth;

ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT 'assets/images/default_avatar.png';

CREATE TABLE IF NOT EXISTS user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    label VARCHAR(50) NOT NULL, -- e.g., 'Home', 'Office'
    recipient_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20),
    address_line TEXT NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Database: CMS Service Updates
USE ngulikpc_cms;

CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(200)
);

INSERT IGNORE INTO site_settings (setting_key, setting_value, description) VALUES 
('site_title', 'NgulikPC', 'Website Main Title'),
('header_text', 'Ultimate Hardware Store', 'Text on Header'),
('footer_text', 'Premium PC Components. Built for Enthusiasts.', 'Text on Footer'),
('welcome_message', 'Build Your Dream Machine', 'Hero Section Title');
