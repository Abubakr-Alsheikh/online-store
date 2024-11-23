<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    // Store the current page URL in the session
    $currentUrl = $_SERVER['REQUEST_URI'];
    $_SESSION['flash_message'] = [
        'type' => 'warning',
        'message' => 'You have to login to continue the proccess.'
    ];
    header("Location: login.php?next=" . urlencode($currentUrl)); // Pass the current URL to login.php
    exit;
}

require_once("includes/database.php");

// Get user ID from the session
$userId = $_SESSION['user_id'];

// Fetch orders for the logged-in user
$ordersResult = Database::query("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC", [$userId]);
$orders = $ordersResult ? $ordersResult : [];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - My Online Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include "includes/header.php"; ?>

    <main class="container mt-5">
        <h2 class="mb-4">My Account</h2>

        <h3>Order History</h3>

        <?php if (!empty($orders)) : ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) : ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></td>
                            <td>$<?php echo number_format($order['total'], 2); ?></td>

                            <td>
                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>You have not placed any orders yet.</p>
        <?php endif; ?>

    </main>

    <?php include "includes/footer.php"; ?>

</body>

</html>