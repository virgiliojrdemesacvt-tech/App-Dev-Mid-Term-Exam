-- Optional: core/dbConfig.php creates / upgrades all of this automatically.
CREATE DATABASE IF NOT EXISTS carrental_db;
USE carrental_db;

-- USER ENTITY (people who log in to the system)
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    address VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    contact_no VARCHAR(20) NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PARENT ENTITY (ONE customer has MANY rentals)
CREATE TABLE IF NOT EXISTS customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    date_of_birth VARCHAR(50),
    license_number TEXT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by INT NULL,
    last_updated_by INT NULL,
    last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_customers_added_by FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL,
    CONSTRAINT fk_customers_last_updated_by FOREIGN KEY (last_updated_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- CHILD ENTITY
CREATE TABLE IF NOT EXISTS rentals (
    rental_id INT AUTO_INCREMENT PRIMARY KEY,
    car_model VARCHAR(50),
    rental_days TEXT,
    customer_id INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by INT NULL,
    last_updated_by INT NULL,
    last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rentals_added_by FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL,
    CONSTRAINT fk_rentals_last_updated_by FOREIGN KEY (last_updated_by) REFERENCES users(user_id) ON DELETE SET NULL
);
