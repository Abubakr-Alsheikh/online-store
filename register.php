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
    <title>Register - My Online Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include "includes/header.php"; ?>

    <main class="container mt-5">
        <h2 class="text-center mb-4">Register</h2>

        <?php
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize user inputs
            $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
            $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
            $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
            $confirmPassword = trim(filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_STRING));

            // Basic validation (You'll likely want more robust validation)
            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                echo '<div class="alert alert-danger">Please fill in all fields.</div>';
            } elseif ($password !== $confirmPassword) {
                echo '<div class="alert alert-danger">Passwords do not match.</div>';
            } else {
                // Hash the password before storing
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Connect to the database
                require_once("includes/database.php");
                $conn = Database::connect();

                // Prepare the SQL query
                $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);

                // Execute the query
                try {
                    $stmt->execute([$username, $email, $passwordHash]);
                    echo '<div class="alert alert-success">Registration successful! You can now <a href="login.php">log in</a>.</div>';
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) { // Duplicate key violation (username or email exists)
                        echo '<div class="alert alert-danger">Username or email already exists.</div>';
                    } else {
                        echo '<div class="alert alert-danger">Registration failed. Please try again later.</div>';
                    }
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
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <div class="alert alert-secondary mt-3 text-center">If you have an account <a href="login.php">Login</a>.</div>

        </form>
    </main>

    <?php include "includes/footer.php"; ?>

</body>

</html>