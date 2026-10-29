<?php
require_once __DIR__ . '/dbConfig.php';
require_once __DIR__ . '/models.php';
require_once __DIR__ . '/auth.php';

/* ================= USER MODULE (no login needed) ================= */

if (isset($_POST['registerBtn'])) {
    $first   = trim($_POST['firstName'] ?? '');
    $last    = trim($_POST['lastName'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $age     = trim($_POST['age'] ?? '');
    $contact = trim($_POST['contact_no'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $uname   = trim($_POST['username'] ?? '');
    $pw      = $_POST['password'] ?? '';
    $pw2     = $_POST['confirm_password'] ?? '';

    $_SESSION['old'] = ['firstName' => $first, 'lastName' => $last, 'address' => $address, 'age' => $age,
                        'contact_no' => $contact, 'email' => $email, 'username' => $uname];

    if ($first === '' || $last === '' || $address === '') {
        setFlash('error', 'First name, last name and address are required.');
    } elseif (!ctype_digit($age) || (int)$age < 1 || (int)$age > 120) {
        setFlash('error', 'Please enter a valid age.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Please enter a valid email address.');
    } elseif (!preg_match('/^[A-Za-z0-9_]{4,30}$/', $uname)) {
        setFlash('error', 'Username must be 4-30 characters: letters, numbers or underscore.');
    } elseif (strlen($pw) < 8) {
        setFlash('error', 'Password must be at least 8 characters.');
    } elseif ($pw !== $pw2) {
        setFlash('error', 'Passwords do not match.');
    } elseif (userExists($pdo, $uname, $email)) {
        setFlash('error', 'That username or email is already taken.');
    } elseif (insertUser($pdo, $first, $last, $address, (int)$age, $contact ?: null, $email, $uname, $pw)) {
        unset($_SESSION['old']);
        setFlash('ok', 'Registration successful! You may now log in.');
        redirectTo('login.php');
    } else {
        setFlash('error', 'Registration failed. Please try again.');
    }
    redirectTo('register.php');
}

if (isset($_POST['loginBtn'])) {
    $uname = trim($_POST['username'] ?? '');
    $user  = getUserByUsername($pdo, $uname);

    if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']   = (int)$user['user_id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
        redirectTo('index.php');
    }
    setFlash('error', 'Invalid username or password.');
    redirectTo('login.php');
}

/* ======= EVERYTHING BELOW NEEDS A LOGGED-IN USER ======= */
requireLogin();
$userId = currentUserId();   // the user who is doing the insert / update

/* ---------- CUSTOMERS (the "one" side) ---------- */

if (isset($_POST['insertCustomerBtn'])) {
    $query = insertCustomer($pdo, $_POST['username'], $_POST['firstName'],
        $_POST['lastName'], $_POST['dateOfBirth'], $_POST['license_number'], $userId);

    if ($query) { header("Location: ../index.php"); exit; }
    echo "Insertion failed";
}

if (isset($_POST['editCustomerBtn'])) {
    $query = updateCustomer($pdo, $_POST['firstName'], $_POST['lastName'],
        $_POST['dateOfBirth'], $_POST['license_number'], requireId('customer_id'), $userId);

    if ($query) { header("Location: ../index.php"); exit; }
    echo "Edit failed";
}

if (isset($_POST['deleteCustomerBtn'])) {
    $query = deleteCustomer($pdo, requireId('customer_id'));

    if ($query) { header("Location: ../index.php"); exit; }
    echo "Deletion failed";
}

/* ---------- RENTALS ---------- */

if (isset($_POST['insertNewRentalBtn'])) {
    $customerId = requireId('customer_id');
    $query = insertRental($pdo, $_POST['carModel'], $_POST['rental_days'], $customerId, $userId);

    if ($query) { header("Location: ../viewrentals.php?customer_id=" . $customerId); exit; }
    echo "Insertion failed";
}

if (isset($_POST['editRentalBtn'])) {
    $customerId = requireId('customer_id');
    $query = updateRental($pdo, $_POST['carModel'], $_POST['rental_days'], requireId('rental_id'), $userId);

    if ($query) { header("Location: ../viewrentals.php?customer_id=" . $customerId); exit; }
    echo "Update failed";
}

if (isset($_POST['deleteRentalBtn'])) {
    $customerId = requireId('customer_id');
    $query = deleteRental($pdo, requireId('rental_id'));

    if ($query) { header("Location: ../viewrentals.php?customer_id=" . $customerId); exit; }
    echo "Deletion failed";
}
