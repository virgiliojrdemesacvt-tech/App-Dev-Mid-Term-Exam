<?php
require_once __DIR__ . '/core/dbConfig.php';
require_once __DIR__ . '/core/models.php';
require_once __DIR__ . '/core/auth.php';
requireLogin();
$id  = requireId('customer_id');
$dev = getCustomerByID($pdo, $id);
if (!$dev) { header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php renderUserBar(); ?>
    <a href="index.php">Return to home</a>
    <h1>Edit the customer!</h1>
    <form action="core/handleForms.php?customer_id=<?php echo $id; ?>" method="POST">
        <p>
            <label for="firstName">First Name</label>
            <input type="text" name="firstName" id="firstName" value="<?php echo e($dev['first_name']); ?>">
        </p>
        <p>
            <label for="lastName">Last Name</label>
            <input type="text" name="lastName" id="lastName" value="<?php echo e($dev['last_name']); ?>">
        </p>
        <p>
            <label for="dateOfBirth">Date of Birth</label>
            <input type="date" name="dateOfBirth" id="dateOfBirth" value="<?php echo e($dev['date_of_birth']); ?>">
        </p>
        <p>
            <label for="license_number">License Number</label>
            <input type="text" name="license_number" id="license_number" value="<?php echo e($dev['license_number']); ?>">
            <input type="submit" name="editCustomerBtn" value="Save">
        </p>
    </form>
</body>
</html>
