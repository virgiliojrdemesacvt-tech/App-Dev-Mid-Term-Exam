<?php
/* ---------- HELPER ---------- */

// Reads an id from the URL (?customer_id=3). Sends the user home if it is missing.
function requireId($key) {
    if (!isset($_GET[$key]) || !ctype_digit((string)$_GET[$key])) {
        $inCore = basename(dirname($_SERVER['SCRIPT_NAME'])) === 'core';
        header("Location: " . ($inCore ? "../index.php" : "index.php"));
        exit;
    }
    return (int)$_GET[$key];
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Shows "—" when nobody is recorded (old records, or the user was removed)
function who($name) {
    return ($name === null || trim($name) === '') ? '—' : $name;
}

/* ---------- USERS (people who log in) ---------- */

function userExists($pdo, $username, $email) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    return $stmt->fetchColumn() > 0;
}

function insertUser($pdo, $first_name, $last_name, $address, $age, $contact_no, $email, $username, $password) {
    $sql = "INSERT INTO users (first_name, last_name, address, age, contact_no, email, username, password_hash)
            VALUES (?,?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$first_name, $last_name, $address, $age, $contact_no, $email, $username,
                           password_hash($password, PASSWORD_DEFAULT)]);
}

function getUserByUsername($pdo, $username) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    return $stmt->fetch();
}

/* ---------- CUSTOMERS (the "one" side) ---------- */

// Reused by the SELECT queries: also fetch the NAMES of the users who added / last updated a row
function whoJoin($alias) {
    return "LEFT JOIN users ua ON {$alias}.added_by = ua.user_id
            LEFT JOIN users uu ON {$alias}.last_updated_by = uu.user_id";
}
function insertCustomer($pdo, $username, $first_name, $last_name, $date_of_birth, $license_number, $user_id) {
    $sql = "INSERT INTO customers (username, first_name, last_name, date_of_birth, license_number, added_by, last_updated_by)
            VALUES (?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$username, $first_name, $last_name, $date_of_birth, $license_number, $user_id, $user_id]);
}

function getAllCustomers($pdo) {
    $sql = "SELECT customers.*,
                   CONCAT(ua.first_name, ' ', ua.last_name) AS added_by_name,
                   CONCAT(uu.first_name, ' ', uu.last_name) AS updated_by_name
            FROM customers " . whoJoin('customers') . "
            ORDER BY customers.customer_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getCustomerByID($pdo, $customer_id) {
    $sql = "SELECT customers.*,
                   CONCAT(ua.first_name, ' ', ua.last_name) AS added_by_name,
                   CONCAT(uu.first_name, ' ', uu.last_name) AS updated_by_name
            FROM customers " . whoJoin('customers') . "
            WHERE customers.customer_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id]);
    return $stmt->fetch();
}

function updateCustomer($pdo, $first_name, $last_name, $date_of_birth, $license_number, $customer_id, $user_id) {
    $sql = "UPDATE customers
            SET first_name = ?, last_name = ?, date_of_birth = ?, license_number = ?,
                last_updated_by = ?, last_updated = NOW()
            WHERE customer_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$first_name, $last_name, $date_of_birth, $license_number, $user_id, $customer_id]);
}

function deleteCustomer($pdo, $customer_id) {
    // Delete the child rows (rentals) first, then the parent
    $stmt = $pdo->prepare("DELETE FROM rentals WHERE customer_id = ?");
    if ($stmt->execute([$customer_id])) {
        $stmt2 = $pdo->prepare("DELETE FROM customers WHERE customer_id = ?");
        return $stmt2->execute([$customer_id]);
    }
    return false;
}

/* ---------- RENTALS (the "many" side) ---------- */

function getRentalsByCustomer($pdo, $customer_id) {
    $sql = "SELECT rentals.rental_id, rentals.car_model, rentals.rental_days,
                   rentals.date_added, rentals.last_updated,
                   CONCAT(customers.first_name, ' ', customers.last_name) AS customer_name,
                   CONCAT(ua.first_name, ' ', ua.last_name) AS added_by_name,
                   CONCAT(uu.first_name, ' ', uu.last_name) AS updated_by_name
            FROM rentals
            JOIN customers ON rentals.customer_id = customers.customer_id
            " . whoJoin('rentals') . "
            WHERE rentals.customer_id = ?
            ORDER BY rentals.rental_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id]);
    return $stmt->fetchAll();
}

function insertRental($pdo, $car_model, $rental_days, $customer_id, $user_id) {
    $sql = "INSERT INTO rentals (car_model, rental_days, customer_id, added_by, last_updated_by) VALUES (?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$car_model, $rental_days, $customer_id, $user_id, $user_id]);
}

function getRentalByID($pdo, $rental_id) {
    $sql = "SELECT rentals.rental_id, rentals.car_model, rentals.rental_days,
                   rentals.date_added, rentals.last_updated,
                   CONCAT(customers.first_name, ' ', customers.last_name) AS customer_name,
                   CONCAT(ua.first_name, ' ', ua.last_name) AS added_by_name,
                   CONCAT(uu.first_name, ' ', uu.last_name) AS updated_by_name
            FROM rentals
            JOIN customers ON rentals.customer_id = customers.customer_id
            " . whoJoin('rentals') . "
            WHERE rentals.rental_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$rental_id]);
    return $stmt->fetch();
}

function updateRental($pdo, $car_model, $rental_days, $rental_id, $user_id) {
    $sql = "UPDATE rentals SET car_model = ?, rental_days = ?, last_updated_by = ?, last_updated = NOW()
            WHERE rental_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$car_model, $rental_days, $user_id, $rental_id]);
}

function deleteRental($pdo, $rental_id) {
    $stmt = $pdo->prepare("DELETE FROM rentals WHERE rental_id = ?");
    return $stmt->execute([$rental_id]);
}
