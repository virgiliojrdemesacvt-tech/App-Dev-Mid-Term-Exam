-- Only needed if you want to upgrade an EXISTING carrental_db by hand in phpMyAdmin.
-- (Opening the system in the browser does the same thing automatically.)
USE carrental_db;

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

ALTER TABLE customers
    ADD COLUMN added_by INT NULL,
    ADD COLUMN last_updated_by INT NULL,
    ADD COLUMN last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ADD CONSTRAINT fk_customers_added_by FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_customers_last_updated_by FOREIGN KEY (last_updated_by) REFERENCES users(user_id) ON DELETE SET NULL;

ALTER TABLE rentals
    ADD COLUMN added_by INT NULL,
    ADD COLUMN last_updated_by INT NULL,
    ADD COLUMN last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ADD CONSTRAINT fk_rentals_added_by FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_rentals_last_updated_by FOREIGN KEY (last_updated_by) REFERENCES users(user_id) ON DELETE SET NULL;
