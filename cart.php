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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - My Online Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include "includes/header.php"; ?>

    <main class="container mt-5">
        <h2 class="mb-4">Your Shopping Cart</h2>

        <div id="cart-content">

            <?php
            require_once("includes/database.php"); // Assuming you have this file for database connection
            // Function to calculate item total
            function calculateItemTotal($price, $quantity)
            {
                return $price * $quantity;
            }
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) :
                $subtotal = 0;
            ?>

                <table class="table table-striped cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $item) :
                            $productId = $item['productId'];

                            // Fetch product details from the database
                            $productResult = Database::query("SELECT * FROM products WHERE id = ?", [$productId]);

                            if ($productResult) :
                                $product = $productResult[0];
                                $itemTotal = calculateItemTotal($product['price'], $item['quantity']);
                                $subtotal += $itemTotal;
                        ?>

                                <tr data-product-id="<?php echo $productId; ?>">
                                    <td>
                                        <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" height="50">
                                        <?php echo $product['name']; ?>
                                    </td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td>
                                        <input type="number" min="1" class="form-control quantity-input" value="<?php echo $item['quantity']; ?>" data-product-id="<?php echo $productId; ?>">
                                    </td>
                                    <td class="item-subtotal">$<?php echo number_format($itemTotal, 2); ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm remove-from-cart" data-product-id="<?php echo $productId; ?>">
                                            Remove
                                        </button>
                                    </td>
                                </tr>

                            <?php endif; // End if product result 
                            ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="row mt-4">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Order Summary</h5>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td id="cart-subtotal">$<?php echo number_format($subtotal, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Shipping:</td>
                                            <td id="cart-shipping">$0.00</td>
                                        </tr>
                                        <tr class="font-weight-bold">
                                            <td>Total:</td>
                                            <td id="cart-total">$<?php echo number_format($subtotal, 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <a href="checkout.php" class="btn btn-primary btn-block" id="checkout-button">Proceed to Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else : ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>

        </div>
    </main>

    <?php include "includes/footer.php"; ?>

    <script>
        $(document).ready(function() {
            // Function to update the cart
            function updateCart(action, productId, quantity = 1) {
                $.ajax({
                    url: 'update_cart.php', // Create this file for handling cart updates
                    type: 'POST',
                    data: {
                        action: action,
                        productId: productId,
                        quantity: quantity
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Update only the necessary elements:
                            if (action === 'remove') {
                                $(`tr[data-product-id="${productId}"]`).remove(); // Remove the row
                                console.log($('.cart-table tbody tr').length);
                                if ($('.cart-table tbody tr').length === 0) {
                                    // If cart is empty, display a message
                                    $('#cart-content').html('<p>Your cart is empty.</p>');
                                }
                            } else if (action === 'update') {
                                // Update the item's subtotal
                                $(`tr[data-product-id="${productId}"] .item-subtotal`).text(response.itemSubtotal);
                            }

                            // Update totals
                            $('#cart-subtotal').text(response.subtotal);
                            $('#cart-total').text(response.total);

                            showToast(response.success);
                        } else {
                            showToast(response.error, 'danger');
                        }
                    },
                    error: function(e) {
                        showToast('An error occurred while updating the cart.', 'danger');
                    }
                });
            }

            // Remove from cart button click
            $('#cart-content').on('click', '.remove-from-cart', function() {
                const productId = $(this).data('product-id');
                updateCart('remove', productId);
            });

            // Quantity input change event
            $('#cart-content').on('change', '.quantity-input', function() {
                const productId = $(this).data('product-id');
                const newQuantity = $(this).val();
                updateCart('update', productId, newQuantity);
            });
        });
    </script>
</body>

</html>