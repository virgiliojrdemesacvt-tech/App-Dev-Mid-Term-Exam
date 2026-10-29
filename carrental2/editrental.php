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
    <title>Edit Rental</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php renderUserBar(); ?>
    <a href="viewrentals.php?customer_id=<?php echo $customerId; ?>">View The Rentals</a>
    <h1>Edit the rental!</h1>
    <form action="core/handleForms.php?rental_id=<?php echo $rentalId; ?>&customer_id=<?php echo $customerId; ?>" method="POST">
        <p>
            <label for="carModel">Car Model</label>
            <input type="text" name="carModel" id="carModel" value="<?php echo e($rental['car_model']); ?>">
        </p>
        <p>
            <label for="rental_days">Rental Days</label>
            <input type="text" name="rental_days" id="rental_days" value="<?php echo e($rental['rental_days']); ?>">
            <input type="submit" name="editRentalBtn" value="Save">
        </p>
    </form>
</body>
</html>
