CREATE DATABASE IF NOT EXISTS lawyer_cms;
USE lawyer_cms;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS practice_areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-balance-scale',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS attorneys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100),
    bio TEXT,
    image VARCHAR(255),
    email VARCHAR(100),
    phone VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT
);

-- Insert default admin user (password is 'admin123' hashed with bcrypt)
INSERT INTO users (username, password, email) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@lawyer-cms.local') ON DUPLICATE KEY UPDATE username=username;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES 
('site_name', 'Justice & Partners'),
('site_email', 'contact@justicepartners.com'),
('site_phone', '+1 (555) 123-4567'),
('site_address', '123 Legal Avenue, Suite 100, New York, NY 10001')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

-- Insert some dummy practice areas
INSERT INTO practice_areas (title, description, icon) VALUES 
('Corporate Law', 'Comprehensive legal solutions for businesses of all sizes, from startups to multinational corporations.', 'fas fa-briefcase'),
('Family Law', 'Compassionate representation in divorce, child custody, and other family-related legal matters.', 'fas fa-users'),
('Criminal Defense', 'Vigorous defense against criminal charges, protecting your rights and freedom.', 'fas fa-gavel')
ON DUPLICATE KEY UPDATE id=id;
