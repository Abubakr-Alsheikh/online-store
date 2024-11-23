<?php

session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - My Online Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include "includes/header.php"; ?>

    <main class="container">
        <h1 class="text-center mt-4 mb-4">Our Products</h1>

        <div class="row">

            <?php
            require_once("includes/database.php");

            // Fetch all products from the database
            $products = Database::query("SELECT * FROM products");

            if ($products) {
                foreach ($products as $product) {
            ?>

                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <a href="product-details.php?id=<?php echo $product['id']; ?>">
                                <img src="images/<?php echo $product['image']; ?>" class="card-img-top" style="height: 300px;" alt="<?php echo $product['name']; ?>">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                <p class="card-text flex-grow-1"><?php echo $product['description']; ?></p>
                                <p class="card-text font-weight-bold">$<?php echo $product['price']; ?> - Stock: <?php echo $product['stock']; ?></p>
                                <div class="btn-group mt-auto" role="group">
                                    <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>

                                    <?php
                                    // Determine button text and action based on cart status
                                    $buttonAction = (isset($_SESSION['cart'][$product['id']])) ? 'remove' : 'add';
                                    $buttonText = (isset($_SESSION['cart'][$product['id']])) ? 'Remove from Cart' : 'Add to Cart';
                                    $buttonType = (isset($_SESSION['cart'][$product['id']])) ? 'danger' : 'success';
                                    ?>
                                    <button class="btn btn-<?php echo $buttonType; ?> add-to-cart" data-product-id="<?php echo $product['id']; ?>" data-action="<?php echo $buttonAction; ?>" data-original-text="<?php echo $buttonText; ?>">
                                        <?php echo $buttonText; ?>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>

            <?php
                }
            } else {
                echo "<p class='text-center'>No products found.</p>";
            }
            ?>

        </div>
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