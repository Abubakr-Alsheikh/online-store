<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You have to login to add it to cart.']);
    exit;
}
require_once("includes/database.php");

// Check if productId is received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['productId'])) {
    $action = $_POST['action'];
    $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;

    if ($action === 'add' && $productId > 0) {

        $product = Database::query("SELECT * FROM products WHERE id = ?", [$productId]);
        $stock = (int)$product[0]["stock"];
        if ($stock <= 0) {
            echo json_encode(['success' => "There is no more of the product anymore. $stock"]);
            exit;
        }

        // Add product to the cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity']++;
        } else {
            // If not, add it to the cart with quantity 1
            $_SESSION['cart'][$productId] = [
                'productId' => $productId,
                'quantity' => 1,
                // ... other product details you want to store in the cart
            ];
        }

        echo json_encode(['success' => 'Product added to cart!']);
    } else if ($action === 'remove' && $productId > 0) {
        // Remove product from the cart
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            echo json_encode(['success' => 'Product removed from cart!']);
        } else {
            echo json_encode(['error' => 'Product is not in the cart.']);
        }
    } else {
        echo json_encode(['error' => 'Invalid request.']);
    }
} else {
    echo json_encode(['error' => 'Product ID not provided.']);
}
