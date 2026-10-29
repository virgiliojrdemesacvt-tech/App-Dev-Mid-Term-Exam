<?php
require_once __DIR__ . '/core/dbConfig.php';
require_once __DIR__ . '/core/models.php';
require_once __DIR__ . '/core/auth.php';
requireLogin();                       // not logged in -> redirected to login.php
$customers = getAllCustomers($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php renderUserBar(); ?>
    <h1>Welcome To The Car Rental System. Register new Customers!</h1>

    <form action="core/handleForms.php" method="POST">
        <p>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
        </p>
        <p>
            <label for="firstName">First Name</label>
            <input type="text" name="firstName" id="firstName" required>
        </p>
        <p>
            <label for="lastName">Last Name</label>
            <input type="text" name="lastName" id="lastName" required>
        </p>
        <p>
            <label for="dateOfBirth">Date of Birth</label>
            <input type="date" name="dateOfBirth" id="dateOfBirth" required>
        </p>
        <p>
            <label for="license_number">License Number</label>
            <input type="text" name="license_number" id="license_number" required>
            <input type="submit" name="insertCustomerBtn" value="Add Customer">
        </p>
    </form>

    <h2>Registered Customers</h2>
    <?php if (count($customers) === 0) { ?>
        <p>No customers yet. Add one above.</p>
    <?php } else { ?>
    <div class="table-wrap">
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Date of Birth</th>
            <th>License Number</th>
            <th>Date Added</th>
            <th>Added By</th>
            <th>Last Updated By</th>
            <th>Last Updated</th>
            <th>Action</th>
        </tr>
        <?php foreach ($customers as $row) { ?>
        <tr>
            <td><?php echo (int)$row['customer_id']; ?></td>
            <td><?php echo e($row['username']); ?></td>
            <td><?php echo e($row['first_name']); ?></td>
            <td><?php echo e($row['last_name']); ?></td>
            <td><?php echo e($row['date_of_birth']); ?></td>
            <td><?php echo e($row['license_number']); ?></td>
            <td><?php echo e($row['date_added']); ?></td>
            <td><?php echo e(who($row['added_by_name'])); ?></td>
            <td><?php echo e(who($row['updated_by_name'])); ?></td>
            <td><?php echo e($row['last_updated']); ?></td>
            <td class="actions">
                <a href="viewrentals.php?customer_id=<?php echo (int)$row['customer_id']; ?>">View Rentals</a>
                <a href="editcustomer.php?customer_id=<?php echo (int)$row['customer_id']; ?>">Edit</a>
                <a href="deletecustomer.php?customer_id=<?php echo (int)$row['customer_id']; ?>">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    </div>
    <?php } ?>
</body>
</html>
