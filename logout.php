<?php
session_start();
$_SESSION = array();
session_destroy();

session_start();
$_SESSION['flash_message'] = [
    'type' => 'success',
    'message' => 'You have logout successfully.'
];
header("Location: index.php");
exit;
