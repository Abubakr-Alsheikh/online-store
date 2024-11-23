<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - My Online Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php
    include "includes/header.php";
    require_once("includes/database.php");
    ?>

    <main class="container mt-5">
        <?php
        // Get product ID from URL parameter
        if (isset($_GET['id'])) {
            $productId = (int)$_GET['id'];

            // Fetch product data from the database
            $product = Database::query("SELECT * FROM products WHERE id = ?", [$productId]);

            // Check if the product exists
            if ($product) {
                $product = $product[0]; // Get the first (and only) result 
        ?>

                <div class="row">
                    <div class="col-md-6">
                        <img src="images/<?php echo $product['image']; ?>" class="img-fluid" alt="<?php echo $product['name']; ?>">
                    </div>
                    <div class="col-md-6">
                        <h2 class="mb-3"><?php echo $product['name']; ?></h2>
                        <p class="text-muted">$<?php echo $product['price']; ?> - Stock: <?php echo $product['stock']; ?></p>
                        <p class="lead"><?php echo $product['description']; ?></p>

                        <?php
                        // Determine button text and action based on cart status
                        $buttonAction = (isset($_SESSION['cart'][$productId])) ? 'remove' : 'add';
                        $buttonText = (isset($_SESSION['cart'][$productId])) ? 'Remove from Cart' : 'Add to Cart';
                        $buttonType = (isset($_SESSION['cart'][$productId])) ? 'danger' : 'success';
                        ?>
                        <button class="btn btn-<?php echo $buttonType; ?> add-to-cart" data-product-id="<?php echo $productId; ?>" data-action="<?php echo $buttonAction; ?>" data-original-text="<?php echo $buttonText; ?>">
                            <?php echo $buttonText; ?>
                        </button>
                    </div>
                </div>

        <?php
            } else {
                // Product not found
                echo '<div class="alert alert-warning">Product not found.</div>';
            }
        } else {
            // Product ID not provided
            echo '<div class="alert alert-danger">Invalid product request.</div>';
        }
        ?>
    </main>

    <?php include "includes/footer.php"; ?>

    <script>
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.dataset.productId;
                const action = button.dataset.action; // Get add/remove action
                showLoadingButton(button);

                // AJAX request
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "add_to_cart.php", true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    resetButton(button);
                    if (this.status >= 200 && this.status < 400) {
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            showToast(response.success);

                            // Toggle button text and action
                            button.dataset.action = (action === 'add') ? 'remove' : 'add';
                            button.dataset.originalText = (action === 'add') ? 'Remove from Cart' : 'Add to Cart';
                            button.innerText = (action === 'add') ? 'Remove from Cart' : 'Add to Cart';
                            button.classList.replace((action === 'add') ? 'btn-success' : 'btn-danger', (action === 'add') ? 'btn-danger' : 'btn-success');

                        } else if (response.error) {
                            showToast(response.error, 'danger');
                        }
                    } else {
                        console.error("Error:", this.status);
                        showToast('An error occurred.', 'danger');
                    }
                };

                xhr.onerror = function() {
                    resetButton(button);
                    console.error("Network error occurred.");
                    showToast('A network error occurred.', 'danger');
                };

                // Send the product ID and action to the server
                xhr.send("productId=" + productId + "&action=" + action);
            });
        });
    </script>

</body>

</html>