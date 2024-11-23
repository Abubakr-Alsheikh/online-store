<footer class="footer mt-5 py-3 bg-light mb-3 container" style="border-radius: 32px !important;">
    <div class="container text-center">
        <span class="text-muted">©<?php echo date("Y"); ?> - <a href="https://abubakr-alsheikh.github.io/my-portfolio/">Abubkar Alsheikh</a></span>
    </div>
</footer>

<?php
if (isset($_SESSION['flash_message'])) {
    echo '<script>sessionStorage.setItem("flash_message", JSON.stringify(' . json_encode($_SESSION['flash_message']) . '));</script>';
    unset($_SESSION['flash_message']);
}
?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="scripts/script.js"></script>