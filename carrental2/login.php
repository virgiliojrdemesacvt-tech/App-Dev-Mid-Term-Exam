<?php
require_once __DIR__ . '/core/dbConfig.php';
require_once __DIR__ . '/core/models.php';
require_once __DIR__ . '/core/auth.php';
if (isLoggedIn()) { header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Car Rental System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Car Rental System - Login</h1>
    <?php showFlash(); ?>

    <form action="core/handleForms.php" method="POST">
        <p>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required autofocus>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            <input type="submit" name="loginBtn" value="Login">
        </p>
    </form>
    <p class="switch">No account yet? <a href="register.php">Register here</a></p>
</body>
</html>
