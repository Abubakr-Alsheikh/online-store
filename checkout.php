<?php

// Check if the user is logged in
// ... (Add your user authentication check here)
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

// Check if the cart is empty
if (empty($_SESSION['cart'])) {
    // Redirect to cart or handle accordingly
    header("Location: cart.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validate and sanitize user inputs from the form 
    //    (Important: Implement thorough validation!)
    $shippingName = $_POST['name']; // Example - replace with your form field names
    $shippingAddress = $_POST['address'];
    // ... other form fields for shipping address

    // 2. Process Order and Update Database (inside a transaction for data integrity)
    try {
        $conn = Database::connect();
        $conn->beginTransaction(); // Start transaction

        // 2.1 Insert Order Details into the 'orders' table
        $userId = $_SESSION['user_id']; // Assuming you have user sessions
        $orderTotal = $_POST['total']; // Get the total from the form
        $insertOrderSQL = "INSERT INTO orders (user_id, order_date, total, shipping_name, shipping_address)
                            VALUES (?, NOW(), ?, ?, ?)"; // Add shipping fields to query
        $stmt = $conn->prepare($insertOrderSQL);
        $stmt->execute([$userId, $orderTotal, $shippingName, $shippingAddress]); // Pass shipping values

        // Get the newly created order ID
        $orderId = $conn->lastInsertId();

        // 2.2 Insert Order Items into the 'order_items' table
        foreach ($_SESSION['cart'] as $item) {
            $productId = $item['productId'];
            $quantity = $item['quantity'];

            // Fetch product price (for accurate record keeping)
            $productResult = Database::query("SELECT price FROM products WHERE id = ?", [$productId]);
            $productPrice = $productResult[0]['price'];

            $insertOrderItemSQL = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertOrderItemSQL);
            $stmt->execute([$orderId, $productId, $quantity, $productPrice]);

            // 2.3 Update Product Stock (important!)
            $updateStockSQL = "UPDATE products SET stock = stock - ? WHERE id = ?";
            $stmt = $conn->prepare($updateStockSQL);
            $stmt->execute([$quantity, $productId]);
        }

        // 3. Commit Transaction if everything is successful
        $conn->commit();

        // 4. Clear the shopping cart
        unset($_SESSION['cart']);

        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Thank you! Your order (#' . $orderId . ') has been placed successfully.'
        ];
        // 5. Redirect to order confirmation or success page
        header("Location: order_success.php?order_id=$orderId");
        exit;
    } catch (PDOException $e) {
        // If any error occurred, rollback the transaction
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
        // Handle error (e.g., log error, display user-friendly message)
    }
}

function calculateItemTotal($price, $quantity)
{
    return $price * $quantity;
}

// If the form hasn't been submitted yet, display the order review and form:
// Calculate cart totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $productResult = Database::query("SELECT price FROM products WHERE id = ?", [$item['productId']]);
    if ($productResult) {
        $productPrice = $productResult[0]['price'];
        $subtotal += calculateItemTotal($productPrice, $item['quantity']);
    }
}
$total = $subtotal; // + shipping (if applicable)

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - My Online Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include "includes/header.php"; ?>

    <main class="container mt-5">
        <h2 class="mb-4">Checkout</h2>

        <div class="row">
            <div class="col-md-6 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Your Cart</span>
                    <span class="badge badge-secondary badge-pill"><?php echo count($_SESSION['cart']); ?></span>
                </h4>
                <ul class="list-group mb-3">
                    <?php foreach ($_SESSION['cart'] as $item) :
                        $productId = $item['productId'];
                        $productResult = Database::query("SELECT * FROM products WHERE id = ?", [$productId]);
                        if ($productResult) :
                            $product = $productResult[0];
                    ?>
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <h6 class="my-0"><?php echo $product['name']; ?></h6>
                                    <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                                </div>
                                <span class="text-muted">$<?php echo number_format(calculateItemTotal($product['price'], $item['quantity']), 2); ?></span>
                            </li>
                    <?php endif;
                    endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Subtotal</span>
                        <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Shipping</span>
                        <strong>$0.00</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Total</span>
                        <strong>$<?php echo number_format($total, 2); ?></strong>
                    </li>
                </ul>
            </div>
            <div class="col-md-6 order-md-1">
                <h4 class="mb-3">Shipping Address</h4>
                <form method="post" action="checkout.php">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <!-- Add more form fields for address, city, state, zip, etc. -->

                    <input type="hidden" name="total" value="<?php echo $total; ?>">
                    <button class="btn btn-primary btn-lg btn-block" type="submit">Place Order</button>
                </form>
            </div>
        </div>

    </main>

    <?php include "includes/footer.php"; ?>

</body>

</html>