<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = [
        'type' => 'warning',
        'message' => 'You are not allowed to go to this page.'
    ];
    header("Location: index.php"); // Pass the current URL to login.php
    exit;
}

require_once("includes/database.php");

// Get the order ID from the URL parameter
if (isset($_GET['order_id'])) {
    $orderId = (int)$_GET['order_id'];

    // Fetch order details from the database
    $orderResult = Database::query(
        "SELECT * FROM orders WHERE id = ? AND user_id = ?",
        [$orderId, $_SESSION['user_id']] // Assuming you're using user sessions
    );

    if ($orderResult) {
        $order = $orderResult[0];

        // Fetch order items
        $orderItemsResult = Database::query("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
        $orderItems = $orderItemsResult ? $orderItemsResult : [];
    } else {
        echo '<div class="alert alert-danger">Invalid order.</div>';
        exit; // Stop execution
    }
} else {
    echo '<div class="alert alert-danger">Order ID missing.</div>';
    exit; // Stop execution
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - My Online Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include "includes/header.php"; ?>

    <main class="container mt-5">
        <h2 class="text-center mb-4">Order Confirmation</h2>

        <?php if (isset($order) && $order) : ?>
            <!-- <div class="alert alert-success">
                <p>Thank you! Your order has been placed successfully.</p>
                <p>Your order ID is: <strong><?php echo $order['id']; ?></strong></p>
            </div> -->

            <h3>Order Details</h3>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($orderItems as $item) :
                        $productResult = Database::query("SELECT * FROM products WHERE id = ?", [$item['product_id']]);
                        $product = $productResult ? $productResult[0] : null;

                        if ($product) :
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                    ?>
                            <tr>
                                <td><?php echo $product['name']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                    <?php endif;
                    endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                        <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-4">
                <h4>Shipping Address</h4>
                <p>
                    Full Name: <?php echo $order['shipping_name']; ?><br>
                    Address : <?php echo $order['shipping_address']; ?>
                    <!-- Display other address fields as needed -->
                </p>
            </div>

        <?php else : ?>
            <div class="alert alert-danger">There was an error retrieving your order details.</div>
        <?php endif; ?>

        <a href="products.php" class="btn btn-primary mt-3">Continue Shopping</a>
        <a href="account.php" class="btn btn-success mt-3">View your oders</a>
    </main>

    <?php include "includes/footer.php"; ?>
</body>

</html>