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
    <title>Delete Customer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php renderUserBar(); ?>
    <a href="index.php">Cancel</a>
    <h1>Are you sure you want to delete this customer?</h1>
    <p>All of their rentals will be deleted too.</p>
    <div class="container">
        <h2>Username: <?php echo e($dev['username']); ?></h2>
        <h2>First Name: <?php echo e($dev['first_name']); ?></h2>
        <h2>Last Name: <?php echo e($dev['last_name']); ?></h2>
        <h2>Date Of Birth: <?php echo e($dev['date_of_birth']); ?></h2>
        <h2>License Number: <?php echo e($dev['license_number']); ?></h2>
        <h2>Date Added: <?php echo e($dev['date_added']); ?></h2>
        <h2>Added By: <?php echo e(who($dev['added_by_name'])); ?></h2>
        <h2>Last Updated By: <?php echo e(who($dev['updated_by_name'])); ?></h2>

        <div class="editAndDelete">
            <form action="core/handleForms.php?customer_id=<?php echo $id; ?>" method="POST">
                <input type="submit" name="deleteCustomerBtn" value="Delete">
            </form>
        </div>
    </div>
</body>
</html>
