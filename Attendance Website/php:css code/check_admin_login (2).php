<?php
session_start();

$admin_username = 'exec'; // Replace with your admin username
$admin_password = 'exec'; // Replace with your admin password

if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit();
    } else {
        echo "Invalid username or password";
        // Redirect back to login or show an error
    }
}
?>