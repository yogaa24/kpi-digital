<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';

$id_user = $_SESSION['id_user'];

// Ambil data user lengkap
$sql_user = "SELECT * FROM tb_users WHERE id='$id_user'";
$result_user = mysqli_query($conn, $sql_user);
$user_data = mysqli_fetch_assoc($result_user);
?>

<!DOCTYPE html>
<html lang="en">
<?php include("pages/part/p_header.php"); ?>

<style>
    .profile-card {
        transition: all 0.3s ease;
    }
    .profile-card:hover {
        /* transform: translateY(-5px); */
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border: 5px solid #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .setting-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    .setting-card:hover {
        /* transform: translateX(5px); */
    }
    .badge-status {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }
</style>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_utama.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>
        
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid" style="font-size:13px;">
                    
                    <!-- ==================== HEADER ==================== -->
                    <div class="row mb-4 mt-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h3 class="fw-bold mb-2">
                                                <i class="bi bi-person-circle me-2"></i>Account Settings
                                            </h3>
                                            <p class="mb-0 opacity-90">Manage your profile and security settings</p>
                                        </div>
                                        <div>
                                            <a href="dashboard-utama" class="btn btn-light">
                                                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== ALERT NOTIFICATIONS ==================== -->
                    <?php if (isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); } ?>

                    <?php if (isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); } ?>

                    <!-- ==================== PROFILE INFO ==================== -->
                    <div class="row mb-4">
                        <div class="col-lg-4">
                            <div class="card shadow-sm border-0 profile-card">
                                <div class="card-body text-center">
                                    <img src="assets/img/profile.png" class="rounded-circle profile-avatar mb-3" alt="Profile">
                                    <h4 class="fw-bold mb-1"><?= $user_data['nama_lngkp'] ?></h4>
                                    <p class="text-muted mb-2"><?= $user_data['jabatan'] ?? 'User' ?></p>
                                    
                                    <hr class="my-3">
                                    
                                    <div class="text-start">
                                        <div class="mb-2">
                                            <small class="text-muted">Department</small>
                                            <p class="mb-0 fw-semibold"><?= $user_data['departement'] ?? '-' ?></p>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Username</small>
                                            <p class="mb-0 fw-semibold">
                                                <i class="bi bi-person-fill text-primary me-1"></i><?= $user_data['username'] ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== SETTINGS CARDS ==================== -->
                        <div class="col-lg-8">
                            
                            <!-- Change Username Card -->
                            <div class="card shadow-sm border-0 setting-card mb-3" style="border-left-color: #0d6efd !important;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="fw-bold text-primary mb-2">
                                                <i class="bi bi-person-fill me-2"></i>Change Username
                                            </h5>
                                            <p class="text-muted mb-3">Update your username for login</p>
                                            
                                            <form method="POST" action="helper/update-username.php" id="formUsername">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Current Username</label>
                                                        <input type="text" class="form-control" value="<?= $user_data['username'] ?>" disabled>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">New Username <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="new_username" required
                                                               pattern="[a-zA-Z0-9_]+" 
                                                               title="Only letters, numbers, and underscore"
                                                               minlength="4" maxlength="20"
                                                               placeholder="Enter new username">
                                                        <small class="text-muted">4-20 characters, alphanumeric & underscore</small>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Confirm with Password <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" name="confirm_password" id="pwd_username" required
                                                                   placeholder="Enter current password">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('pwd_username')">
                                                                <i class="bi bi-eye" id="icon_pwd_username"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="bi bi-check-circle me-1"></i>Update Username
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Change Password Card -->
                            <div class="card shadow-sm border-0 setting-card mb-3" style="border-left-color: #ffc107 !important;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="w-100">
                                            <h5 class="fw-bold text-warning mb-2">
                                                <i class="bi bi-key-fill me-2"></i>Change Password
                                            </h5>
                                            <p class="text-muted mb-3">Update your password to keep your account secure</p>
                                            
                                            <form method="POST" action="helper/update-password.php" id="formPassword">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" name="current_password" id="current_pwd" required
                                                                   placeholder="Enter current password">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('current_pwd')">
                                                                <i class="bi bi-eye" id="icon_current_pwd"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" name="new_password" id="new_pwd" required
                                                                   minlength="3" maxlength="50"
                                                                   placeholder="Enter new password">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_pwd')">
                                                                <i class="bi bi-eye" id="icon_new_pwd"></i>
                                                            </button>
                                                        </div>
                                                        <small class="text-muted">Minimum 3 characters</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" name="confirm_new_password" id="confirm_pwd" required
                                                                   placeholder="Confirm new password">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('confirm_pwd')">
                                                                <i class="bi bi-eye" id="icon_confirm_pwd"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div id="passwordMatchError" class="alert alert-danger d-none">
                                                            <i class="bi bi-exclamation-triangle me-2"></i>Passwords do not match!
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-warning">
                                                            <i class="bi bi-shield-lock me-1"></i>Update Password
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </main>

        <?php include("pages/part/p_footer.php"); ?>
    </div>

    <!-- ==================== JAVASCRIPT ==================== -->
    <script>
        // Toggle password visibility
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById('icon_' + fieldId);
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Validate password match
        document.getElementById('formPassword').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_pwd').value;
            const confirmPassword = document.getElementById('confirm_pwd').value;
            const errorDiv = document.getElementById('passwordMatchError');
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                errorDiv.classList.remove('d-none');
                return false;
            } else {
                errorDiv.classList.add('d-none');
            }
        });

        // Hide error when typing
        document.getElementById('confirm_pwd').addEventListener('input', function() {
            const errorDiv = document.getElementById('passwordMatchError');
            errorDiv.classList.add('d-none');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

</body>
</html>