<?php
session_start();
require 'config.php';
require 'auth.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index");
    exit();
}

$id_user = $_SESSION['id_user'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_new_password = $_POST['confirm_new_password'];

// Validasi input
if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
    $_SESSION['error'] = "All fields are required!";
    header("Location: ../profile-settings");
    exit();
}

// Validasi password match
if ($new_password !== $confirm_new_password) {
    $_SESSION['error'] = "New passwords do not match!";
    header("Location: ../profile-settings");
    exit();
}

// Validasi panjang password
if (strlen($new_password) < 6) {
    $_SESSION['error'] = "Password must be at least 6 characters long!";
    header("Location: ../profile-settings");
    exit();
}

// Validasi password tidak sama dengan yang lama
if ($current_password === $new_password) {
    $_SESSION['error'] = "New password must be different from current password!";
    header("Location: ../profile-settings");
    exit();
}

// Ambil password lama
$sql_auth = "SELECT password FROM tb_users WHERE id='$id_user'";
$result = mysqli_query($conn, $sql_auth);

if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "User authentication not found!";
    header("Location: ../profile-settings");
    exit();
}

$auth = mysqli_fetch_assoc($result);

if (!verifyUserPassword($current_password, $auth['password'])) {
    $_SESSION['error'] = "Current password is incorrect!";
    header("Location: ../profile-settings");
    exit();
}

$hashedPassword = mysqli_real_escape_string($conn, hashUserPassword($new_password));
$sql_update = "UPDATE tb_users SET password='$hashedPassword' WHERE id='$id_user'";

if (mysqli_query($conn, $sql_update)) {
    $_SESSION['success'] = "Password updated successfully! Please remember your new password.";

    // Log activity (optional)
    $log_sql = "INSERT INTO tb_activity_log (id_user, action, description, created_at) 
                VALUES ('$id_user', 'UPDATE_PASSWORD', 'Changed account password', NOW())";
    mysqli_query($conn, $log_sql);
} else {
    $_SESSION['error'] = "Failed to update password: " . mysqli_error($conn);
}

header("Location: ../profile-settings");
exit();
?>
