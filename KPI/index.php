<?php
session_start();
require 'helper/config.php';

if (isset($_SESSION['id_user'])) {
    // Redirect berdasarkan level
    require 'helper/checkAdmin.php';
    
    if (isAdminHRD()) {
        header("Location: dashboard-adminhrd");
        exit();
    } else {
        header("Location: dashboard-utama");
        exit();
    }
}
 
if (isset($_POST['submit'])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $sql = "SELECT id_user, level FROM tb_auth INNER JOIN tb_users ON tb_users.id = tb_auth.id_user WHERE tb_users.username = '".$username."' AND tb_auth.password = '".$password."';";
    $result = mysqli_query($conn, $sql);
 
    if ($result->num_rows > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['level'] = $row['level'];
        
        // Redirect berdasarkan level
        if ($row['level'] == 7) {
            // Admin HRD
            header("Location: dashboard-adminhrd");
        } else {
            // User biasa
            header("Location: dashboard-utama");
        }
        exit();
    } else {
        echo "<script>alert('Email atau password Anda salah. Silakan coba lagi!')</script>";
    }
}
?>
 
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/style.css">
    <title>KPI Digital | Login</title>
</head>
<body>
    <div class="container">
        <form action="" method="POST" class="login-username">
            <p class="login-text" style="font-size: 2rem; font-weight: 800; margin-bottom:15px; margin-top: -15px;">Login</p>
            <div class="input-group">
                <input type="username" placeholder="username" name="username" required>
            </div>
            <div class="input-group">
                <input type="password" placeholder="Password" name="password" required>
            </div>
            <div class="input-group" style=" margin-top: 15px;">
                <button name="submit" class="btn">Login</button>
            </div>
            <!-- <p class="login-register-text"><a href="register">Register?, </a> Hubungi IT</p> -->
        </form>
    </div>
</body>
</html>
