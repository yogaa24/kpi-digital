<?php
session_start();
require 'config.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index");
    exit();
}

$id_user = $_SESSION['id_user'];
$new_username = mysqli_real_escape_string($conn, trim($_POST['new_username']));
$confirm_password = $_POST['confirm_password'];

// Validasi input
if (empty($new_username) || empty($confirm_password)) {
    $_SESSION['error'] = "All fields are required!";
    header("Location: ../profile-settings");
    exit();
}

// Validasi format username
if (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $new_username)) {
    $_SESSION['error'] = "Username must be 4-20 characters (letters, numbers, underscore only)!";
    header("Location: ../profile-settings");
    exit();
}

// Cek apakah username sudah digunakan
$check_username = mysqli_query($conn, "SELECT id FROM tb_users WHERE username='$new_username' AND id != '$id_user'");
if (mysqli_num_rows($check_username) > 0) {
    $_SESSION['error'] = "Username already taken! Please choose another one.";
    header("Location: ../profile-settings");
    exit();
}

// Ambil password dari tb_auth
$sql_auth = "SELECT password FROM tb_auth WHERE id_user='$id_user'";
$result = mysqli_query($conn, $sql_auth);
$auth = mysqli_fetch_assoc($result);

if (!$auth) {
    $_SESSION['error'] = "User authentication not found!";
    header("Location: ../profile-settings");
    exit();
}

// CEK LANGSUNG (PLAIN TEXT)
if ($confirm_password !== $auth['password']) {
    $_SESSION['error'] = "Current password is incorrect!";
    header("Location: ../profile-settings");
    exit();
}

// Update username
$sql_update = "UPDATE tb_users SET username='$new_username' WHERE id='$id_user'";
if (mysqli_query($conn, $sql_update)) {
    $_SESSION['username'] = $new_username;
    $_SESSION['success'] = "Username updated successfully to '$new_username'!";
    
    // Log activity (optional)
    $log_sql = "INSERT INTO tb_activity_log (id_user, action, description, created_at) 
                VALUES ('$id_user', 'UPDATE_USERNAME', 'Changed username to $new_username', NOW())";
    mysqli_query($conn, $log_sql);
} else {
    $_SESSION['error'] = "Failed to update username: " . mysqli_error($conn);
}

header("Location: ../profile-settings");
exit();
?>