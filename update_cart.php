<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You have to login to edit the cart.']);
    exit;
}

require_once("includes/database.php");

// Function to calculate item total 
function calculateItemTotal($price, $quantity)
{
    return $price * $quantity;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
    $successMessage = 'Cart updated.'; // Default success message

    if ($action === 'remove' && $productId > 0) {
        // Remove item from cart
        unset($_SESSION['cart'][$productId]);
        $successMessage = 'Item removed from cart.';
    } else if ($action === 'update' && $productId > 0 && isset($_POST['quantity'])) {
        // Update item quantity
        $newQuantity = (int)$_POST['quantity'];

        $product = Database::query("SELECT * FROM products WHERE id = ?", [$productId]);

        $stock = (int)$product[0]["stock"];
        if ($stock < $newQuantity) {
            echo json_encode(['error' => "There is no more than $stock, please reduce the number"]);
            exit;
        }

        if ($newQuantity > 0) {
            $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
            $successMessage = 'Quantity updated.';
        } else {
            echo json_encode(['error' => 'Invalid quantity.']);
            exit;
        }
    } else {
        echo json_encode(['error' => 'Invalid request.']);
        exit;
    }

    // Recalculate totals
    $subtotal = 0;
    $itemSubtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $productResult = Database::query("SELECT price FROM products WHERE id = ?", [$item['productId']]);
        if ($productResult) {
            $productPrice = $productResult[0]['price'];
            $subtotal += calculateItemTotal($productPrice, $item['quantity']);

            // Calculate itemSubtotal for the updated/removed product
            if ($item['productId'] == $productId) {
                $itemSubtotal = calculateItemTotal($productPrice, $item['quantity']);
            }
        }
    }
    $total = $subtotal; // + shipping

    // Send JSON response
    echo json_encode([
        'success' => $successMessage,
        'subtotal' => '$' . number_format($subtotal, 2),
        'total' => '$' . number_format($total, 2),
        'itemSubtotal' => '$' . number_format($itemSubtotal, 2)
    ]);
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
