<?php
// Show errors while learning
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ---- CHANGE THESE IF NEEDED ----
$host     = "localhost";
$port     = "3306";        // XAMPP default. MAMP often uses 8889
$user     = "root";
$password = "";            // XAMPP: empty.  MAMP: "root"
$dbname   = "carrental_db";
// --------------------------------

/* ---------- Small helpers used to upgrade an EXISTING database safely ---------- */

function columnExists($pdo, $table, $column) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS
                           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function addColumnIfMissing($pdo, $table, $column, $definition) {
    if (!columnExists($pdo, $table, $column)) {
        $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
    }
}

function addUserForeignKeyIfMissing($pdo, $table, $column, $constraint) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
                           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?");
    $stmt->execute([$table, $constraint]);
    if ($stmt->fetchColumn() == 0) {
        try {
            $pdo->exec("ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraint}`
                        FOREIGN KEY (`{$column}`) REFERENCES `users`(`user_id`) ON DELETE SET NULL");
        } catch (PDOException $e) {
            // Foreign keys are a bonus - the system still works without them
        }
    }
}

try {
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    // 1) Connect to the MySQL server and create the database if it is missing
    $server = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $password, $options);
    $server->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4");

    // 2) Connect to that database
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $user, $password, $options);

    // 3) USER ENTITY (the people who log in to the system)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
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
    )");

    // 4) Your original tables (parent = customers, child = rentals)
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers (
        customer_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50),
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        date_of_birth VARCHAR(50),
        license_number TEXT,
        date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS rentals (
        rental_id INT AUTO_INCREMENT PRIMARY KEY,
        car_model VARCHAR(50),
        rental_days TEXT,
        customer_id INT,
        date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 5) "WHO DID IT" columns - added to old tables too, so existing data is kept
    foreach (['customers' => 'customers', 'rentals' => 'rentals'] as $table) {
        addColumnIfMissing($pdo, $table, 'added_by', 'INT NULL');
        addColumnIfMissing($pdo, $table, 'last_updated_by', 'INT NULL');
        addColumnIfMissing($pdo, $table, 'last_updated',
            'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        addUserForeignKeyIfMissing($pdo, $table, 'added_by', "fk_{$table}_added_by");
        addUserForeignKeyIfMissing($pdo, $table, 'last_updated_by', "fk_{$table}_last_updated_by");
    }
} catch (PDOException $e) {
    die("<h2>Database connection problem</h2>
         <p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>
         <p>Check that MySQL is running in XAMPP, then check the host, port, user and password in <code>core/dbConfig.php</code>.</p>");
}
