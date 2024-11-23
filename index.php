<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include "includes/header.php"; ?>

    <main class="container">
        <div class="jumbotron text-center mt-4">
            <h1 class="display-4">Welcome to Our Store!</h1>
            <p class="lead">Browse our selection of amazing products.</p>
            <a href="products.php" class="btn btn-primary btn-lg">Shop Now</a>
        </div>

        <h2 class="text-center mb-4">Featured Products</h2>

        <div class="row">

            <?php
            // Fetch featured products from database
            require_once("includes/database.php");
            $featuredProducts = Database::query("SELECT * FROM products WHERE featured = 1 LIMIT 6");

            if ($featuredProducts) {
                foreach ($featuredProducts as $product) {
            ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <a href="product-details.php?id=<?php echo $product['id']; ?>">
                                <img src="images/<?php echo $product['image']; ?>" class="card-img-top" style="height: 300px;" alt="<?php echo $product['name']; ?>">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                <p class="card-text"><?php echo $product['description']; ?></p>
                                <p class="card-text">$<?php echo $product['price']; ?></p>
                                <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>No featured products found.</p>";
            }
            ?>

        </div>

    </main>
    <?php include "includes/footer.php"; ?>


</body>

</html>