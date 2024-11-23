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

// Check if the user is logged in 
// ... your authentication logic here ...

// Get the order ID from the URL parameter
if (isset($_GET['id'])) {
    $orderId = (int)$_GET['id'];

    // Fetch order details from the database
    $orderResult = Database::query(
        "SELECT * FROM orders WHERE id = ? AND user_id = ?",
        [$orderId, $_SESSION['user_id']]
    );

    if ($orderResult) {
        $order = $orderResult[0];

        // Fetch order items for the order
        $orderItemsResult = Database::query("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
        $orderItems = $orderItemsResult ? $orderItemsResult : [];
    } else {
        // Handle invalid order ID or unauthorized access
        echo '<div class="alert alert-danger">Invalid order.</div>';
        exit;
    }
} else {
    // Handle missing order ID parameter
    echo '<div class="alert alert-danger">Order ID missing.</div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - My Online Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include "includes/header.php"; ?>

    <main class="container mt-5">
        <h2 class="mb-4">Order Details</h2>

        <?php if (isset($order) && $order) : ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Order ID: #<?php echo $order['id']; ?></h4>
                    <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
                </div>
            </div>

            <h3>Order Items</h3>
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
                        <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                    </tr>
                    <!-- Add shipping costs and calculations if needed -->
                    <tr class="font-weight-bold">
                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <h3 class="mt-5">Shipping Address</h3>
            <address>
                Full Name: <?php echo $order['shipping_name']; ?><br>
                Address : <?php echo $order['shipping_address']; ?>
                <!-- Display other address details as needed -->
            </address>

        <?php else : ?>
            <div class="alert alert-danger">Order not found.</div>
        <?php endif; ?>

        <a href="account.php" class="btn btn-secondary mt-3">Back to My Account</a>
    </main>

    <?php include "includes/footer.php"; ?>

</body>

</html>