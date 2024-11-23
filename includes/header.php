<nav class="navbar navbar-expand-lg navbar-light bg-light mt-4 container" style="border-radius: 32px !important;">
    <a class="navbar-brand" href="index.php">Online Store</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="products.php">Products</a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="cart.php">Cart</a>
            </li>
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<li class="nav-item ' . (basename($_SERVER['PHP_SELF']) == 'account.php' ? 'active' : '') . '"><a class="nav-link" href="account.php">Account</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
            } else {
                echo '<li class="nav-item ' . (basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : '') . '"><a class="nav-link" href="login.php">Login</a></li>';
                echo '<li class="nav-item ' . (basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : '') . '"><a class="nav-link" href="register.php">Register</a></li>';
            }
            ?>
        </ul>
    </div>
</nav>

<div aria-live="polite" aria-atomic="true" style="position: fixed; top: 1rem; right: 1rem; z-index: 1050;">
    <div id="toast-container">
    </div>
</div>