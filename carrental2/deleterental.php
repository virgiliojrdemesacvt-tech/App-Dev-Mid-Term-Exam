<?php
require_once __DIR__ . '/core/dbConfig.php';
require_once __DIR__ . '/core/models.php';
require_once __DIR__ . '/core/auth.php';
requireLogin();
$rentalId = requireId('rental_id');
$customerId  = requireId('customer_id');
$rental   = getRentalByID($pdo, $rentalId);
if (!$rental) { header("Location: viewrentals.php?customer_id=" . $customerId); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Rental</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php renderUserBar(); ?>
    <a href="viewrentals.php?customer_id=<?php echo $customerId; ?>">Cancel</a>
    <h1>Are you sure you want to delete this rental?</h1>
    <div class="container">
        <h2>Car Model: <?php echo e($rental['car_model']); ?></h2>
        <h2>Rental Days: <?php echo e($rental['rental_days']); ?></h2>
        <h2>Customer: <?php echo e($rental['customer_name']); ?></h2>
        <h2>Date Added: <?php echo e($rental['date_added']); ?></h2>
        <h2>Added By: <?php echo e(who($rental['added_by_name'])); ?></h2>
        <h2>Last Updated By: <?php echo e(who($rental['updated_by_name'])); ?></h2>

        <div class="editAndDelete">
            <form action="core/handleForms.php?rental_id=<?php echo $rentalId; ?>&customer_id=<?php echo $customerId; ?>" method="POST">
                <input type="submit" name="deleteRentalBtn" value="Delete">
            </form>
        </div>
    </div>
</body>
</html>
