-- CREATE DATABASE IF NOT EXISTS fish_shop;
-- USE fish_shop;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default_fish.jpg',
    category VARCHAR(50) DEFAULT 'Freshwater',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT IGNORE INTO users (username, email, password, role) VALUES
('priya', 'priya@fishshop.com', 'priya8832', 'admin');

INSERT IGNORE INTO products (name, description, price, image, category) VALUES
('Goldfish', 'Beautiful orange goldfish, perfect for beginners. Easy to care for and very colorful.', 5.99, 'goldfish.jpg', 'Freshwater'),
('Betta Fish', 'Stunning Siamese fighting fish with vibrant flowing fins. Needs its own tank.', 12.99, 'betta.jpg', 'Freshwater'),
('Angelfish', 'Elegant freshwater fish with unique triangular shape. Grows up to 6 inches.', 8.49, 'angelfish.jpg', 'Freshwater'),
('Clownfish', 'Famous orange and white saltwater fish. Great for reef tanks.', 18.99, 'clownfish.jpg', 'Saltwater'),
('Blue Tang', 'Beautiful blue surgeonfish, popular in marine aquariums.', 25.99, 'bluetang.jpg', 'Saltwater'),
('Neon Tetra', 'Small colorful schooling fish that glow under light. Perfect for community tanks.', 2.99, 'neontetra.jpg', 'Freshwater'),
('Guppy', 'Tiny live-bearing fish with beautiful tail patterns. Very easy to breed.', 3.49, 'guppy.jpg', 'Freshwater'),
('Molly Fish', 'Peaceful community fish available in many colors. Adapts well to various conditions.', 4.99, 'molly.jpg', 'Freshwater');
