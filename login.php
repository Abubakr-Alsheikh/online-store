<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Store the current page URL in the session
    $_SESSION['flash_message'] = [
        'type' => 'warning',
        'message' => 'You are already login!'
    ];
    header("Location: account.php"); // Pass the current URL to login.php
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My Online Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include "includes/header.php"; ?>

    <main class="container mt-5">
        <h2 class="text-center mb-4">Login</h2>

        <?php

        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize user inputs
            $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
            $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));

            // Basic validation
            if (empty($username) || empty($password)) {
                echo '<div class="alert alert-danger">Please fill in all fields.</div>';
            } else {
                // Connect to the database
                require_once("includes/database.php");
                $conn = Database::connect();

                // Prepare the SQL query
                $sql = "SELECT * FROM users WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$username]);

                // Check if user exists
                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    // Verify password
                    if (password_verify($password, $user['password_hash'])) {
                        // Set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        // Redirect to the intended page
                        $next = isset($_GET['next']) ? urldecode($_GET['next']) : 'index.php'; // Default to homepage
                        header("Location: $next"); // Redirect to the page
                        exit;
                    } else {
                        echo '<div class="alert alert-danger">Incorrect password.</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger">User not found.</div>';
                }
            }
        }
        ?>

        <form method="post" class="col-12 mx-auto col-md-6">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <div class="alert alert-secondary mt-3 text-center">If you don't have account <a href="register.php">Register</a>.</div>
        </form>
    </main>

    <?php include "includes/footer.php"; ?>

</body>

</html>