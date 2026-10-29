<?php
require_once __DIR__ . '/core/dbConfig.php';
require_once __DIR__ . '/core/models.php';
require_once __DIR__ . '/core/auth.php';
requireLogin();
$id  = requireId('customer_id');
$dev = getCustomerByID($pdo, $id);
if (!$dev) { header("Location: index.php"); exit; }
$rentals = getRentalsByCustomer($pdo, $id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rentals</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php renderUserBar(); ?>
    <a href="index.php">Return to home</a>
    <h1>Rentals of <?php echo e($dev['first_name'] . ' ' . $dev['last_name']); ?></h1>

    <form action="core/handleForms.php?customer_id=<?php echo $id; ?>" method="POST">
        <p>
            <label for="carModel">Car Model</label>
            <input type="text" name="carModel" id="carModel" required>
        </p>
        <p>
            <label for="rental_days">Rental Days</label>
            <input type="text" name="rental_days" id="rental_days" required>
            <input type="submit" name="insertNewRentalBtn" value="Add Rental">
        </p>
    </form>

    <div class="table-wrap">
    <table style="width:100%; margin-top: 50px;">
        <tr>
            <th>Rental ID</th>
            <th>Car Model</th>
            <th>Rental Days</th>
            <th>Customer</th>
            <th>Date Added</th>
            <th>Added By</th>
            <th>Last Updated By</th>
            <th>Last Updated</th>
            <th>Action</th>
        </tr>
        <?php foreach ($rentals as $row) { ?>
        <tr>
            <td><?php echo (int)$row['rental_id']; ?></td>
            <td><?php echo e($row['car_model']); ?></td>
            <td><?php echo e($row['rental_days']); ?></td>
            <td><?php echo e($row['customer_name']); ?></td>
            <td><?php echo e($row['date_added']); ?></td>
            <td><?php echo e(who($row['added_by_name'])); ?></td>
            <td><?php echo e(who($row['updated_by_name'])); ?></td>
            <td><?php echo e($row['last_updated']); ?></td>
            <td>
                <a href="editrental.php?rental_id=<?php echo (int)$row['rental_id']; ?>&customer_id=<?php echo $id; ?>">Edit</a>
                <a href="deleterental.php?rental_id=<?php echo (int)$row['rental_id']; ?>&customer_id=<?php echo $id; ?>">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    </div>
</body>
</html>
