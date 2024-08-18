-- Create the database
CREATE DATABASE HamroSportsDokan;

-- Use the newly created database
USE HamroSportsDokan;

-- Create the 'products' table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    categories ENUM('footballshoes', 'footwear', 'shorts', 'tshirt', 'jackets', 'footballjersey') NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    size ENUM('small', 'medium', 'large', 'extralarge') NOT NULL
);



-- Create the 'user_reg' table
CREATE TABLE user_reg (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    address TEXT,
    street_code_name VARCHAR(255),
    password_hash VARCHAR(255) NOT NULL,
    confirm_password_hash VARCHAR(255) NOT NULL,
    privilege ENUM('user', 'admin', 'guest') DEFAULT 'user'
);

-- Create the 'user_validation' table
CREATE TABLE user_validation (
    email VARCHAR(255) PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL,
    privilege VARCHAR(255)NOT NULL,
    FOREIGN KEY(privilege) REFERENCES user_reg(privilege),

);
-- Create the 'order' table
CREATE TABLE `order` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    quantity INT NOT NULL,
    product_title VARCHAR(255) NOT NULL,
    size ENUM('small', 'medium', 'large', 'extralarge') NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user_reg(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

