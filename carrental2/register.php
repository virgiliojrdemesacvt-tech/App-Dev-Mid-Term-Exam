<?php
require_once __DIR__ . '/core/dbConfig.php';
require_once __DIR__ . '/core/models.php';
require_once __DIR__ . '/core/auth.php';
if (isLoggedIn()) { header("Location: index.php"); exit; }
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Car Rental System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Create your account</h1>
    <?php showFlash(); ?>

    <form action="core/handleForms.php" method="POST">
        <p>
            <label for="firstName">First Name</label>
            <input type="text" name="firstName" id="firstName" value="<?php echo e($old['firstName'] ?? ''); ?>" required>
        </p>
        <p>
            <label for="lastName">Last Name</label>
            <input type="text" name="lastName" id="lastName" value="<?php echo e($old['lastName'] ?? ''); ?>" required>
        </p>
        <p>
            <label for="address">Address</label>
            <input type="text" name="address" id="address" value="<?php echo e($old['address'] ?? ''); ?>" required>
        </p>
        <p>
            <label for="age">Age</label>
            <input type="number" name="age" id="age" min="1" max="120" value="<?php echo e($old['age'] ?? ''); ?>" required>
        </p>
        <p>
            <label for="contact_no">Contact No.</label>
            <input type="text" name="contact_no" id="contact_no" value="<?php echo e($old['contact_no'] ?? ''); ?>">
        </p>
        <p>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo e($old['email'] ?? ''); ?>" required>
        </p>
        <p>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo e($old['username'] ?? ''); ?>" required>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" minlength="8" required>
        </p>
        <p>
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" minlength="8" required>
            <input type="submit" name="registerBtn" value="Register">
        </p>
    </form>
    <p class="switch">Already have an account? <a href="login.php">Log in</a></p>
</body>
</html>
