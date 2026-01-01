<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';

// Koneksi ke database simulasi
$conn_sim = mysqli_connect("localhost", "root", "", "db_simulasi");

// Cek level Admin HRD
$sql_check = "SELECT level FROM tb_auth WHERE id_user = '$id_user'";
$result_check = mysqli_query($conn, $sql_check);
$user_data = mysqli_fetch_assoc($result_check);

if ($user_data['level'] != 5) {
    header("Location: dashboard");
    exit();
}

// Ambil semua data user
$sql_users = "SELECT u.*, a.level FROM tb_users u 
              INNER JOIN tb_auth a ON u.id = a.id_user 
              ORDER BY u.nama_lngkp ASC";
$result_users = mysqli_query($conn, $sql_users);

// Handle Delete
if (isset($_GET['delete'])) {
    $id_delete = $_GET['delete'];
    
    // Delete dari tb_auth
    mysqli_query($conn, "DELETE FROM tb_auth WHERE id_user = '$id_delete'");
    
    // Delete dari tb_users
    mysqli_query($conn, "DELETE FROM tb_users WHERE id = '$id_delete'");
    
    // Delete dari db_simulasi - tb_bobotkpi
    mysqli_query($conn_sim, "DELETE FROM tb_bobotkpi WHERE id_user = '$id_delete'");
    
    // Delete dari db_simulasi - tb_kpi
    mysqli_query($conn_sim, "DELETE FROM tb_kpi WHERE id_user = '$id_delete'");
    
    echo "<script>alert('User berhasil dihapus'); window.location.href='datauser-adminhrd';</script>";
}

// Handle Edit
if (isset($_POST['edit_user'])) {
    $id_edit = $_POST['id_user'];
    $username = $_POST['username'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $nik = $_POST['nik'];
    $bagian = $_POST['bagian'];
    $departement = $_POST['departement'];
    $jabatan = $_POST['jabatan'];
    $atasan = $_POST['atasan'];
    $penilai = $_POST['penilai'];
    
    $sql_update = "UPDATE tb_users SET 
                   username = '$username',
                   nama_lngkp = '$nama_lengkap',
                   nik = '$nik',
                   bagian = '$bagian',
                   departement = '$departement',
                   jabatan = '$jabatan',
                   atasan = '$atasan',
                   penilai = '$penilai'
                   WHERE id = '$id_edit'";
    
    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Data user berhasil diupdate'); window.location.href='datauser-adminhrd';</script>";
    }
}

if (isset($_POST['edit_password'])) {
    $id_edit_pass = $_POST['id_user_pass'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password == $confirm_password) {
        $sql_update_pass = "UPDATE tb_auth SET password = '$new_password' WHERE id_user = '$id_edit_pass'";
        
        if (mysqli_query($conn, $sql_update_pass)) {
            echo "<script>alert('Password berhasil diubah'); window.location.href='datauser-adminhrd';</script>";
        } else {
            echo "<script>alert('Gagal mengubah password');</script>";
        }
    } else {
        echo "<script>alert('Password tidak cocok');</script>";
    }
}

// Handle Register User Baru
if (isset($_POST['register_user'])) {
    $username = $_POST['username'];
    $namalengkap = $_POST['namalengkap'];
    $nik = $_POST['nik'];
    $departemen = $_POST['departemen'];
    $jabatan = $_POST['jabatan'];
    $bagian = $_POST['bagian'];
    $atasan = $_POST['atasan'];
    $penilai = $_POST['penilai'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    
    if ($password == $cpassword) {
        // Cek apakah username sudah ada
        $check_username = mysqli_query($conn, "SELECT * FROM tb_users WHERE username='$username'");
        
        if (mysqli_num_rows($check_username) == 0) {
            // Insert ke tb_users
            $sql_insert = "INSERT INTO tb_users (`username`, `nama_lngkp`, `nik`, `bagian`, `departement`, `jabatan`, `atasan`, `penilai`)
                          VALUES ('$username','$namalengkap','$nik','$bagian','$departemen','$jabatan','$atasan','$penilai')";
            
            if (mysqli_query($conn, $sql_insert)) {
                // Ambil ID user yang baru dibuat
                $new_user_id = mysqli_insert_id($conn);
                
                // Tentukan level berdasarkan jabatan
                $level = 1; // Default Karyawan
                if ($jabatan == 'Kabag') {
                    $level = 2;
                } elseif ($jabatan == 'Kadep') {
                    $level = 3;
                }
                
                // Insert ke tb_auth
                $sql_auth = "INSERT INTO tb_auth (`id_user`, `password`, `level`) VALUES ('$new_user_id', '$password', '$level')";
                
                // Insert ke tb_bobotkpi
                $sql_bobot = "INSERT INTO tb_bobotkpi (`id_user`, `bobotwhat`, `bobothow`) VALUES ('$new_user_id', 0, 0)";
                
                // Insert ke db_simulasi - tb_bobotkpi
                $sql_bobot_simulasi = "INSERT INTO tb_bobotkpi (`id_user`, `bobotwhat`, `bobothow`) VALUES ('$new_user_id', 0, 0)";
                
                // Eksekusi semua query
                $success = true;
                
                if (!mysqli_query($conn, $sql_auth)) {
                    $success = false;
                    $error_msg = "Gagal menambahkan data auth";
                }
                
                if (!mysqli_query($conn, $sql_bobot)) {
                    $success = false;
                    $error_msg = "Gagal menambahkan data bobot";
                }
                
                if (!mysqli_query($conn_sim, $sql_bobot_simulasi)) {
                    $success = false;
                    $error_msg = "Gagal menambahkan data bobot ke db_simulasi";
                }
                
                if ($success) {
                    echo "<script>alert('User berhasil ditambahkan beserta data KPI'); window.location.href='datauser-adminhrd';</script>";
                } else {
                    echo "<script>alert('$error_msg');</script>";
                }
            } else {
                echo "<script>alert('Gagal menambahkan user');</script>";
            }
        } else {
            echo "<script>alert('Username sudah terdaftar');</script>";
        }
    } else {
        echo "<script>alert('Password tidak cocok');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_adminhrd.php"); ?>
        <?php include("pages/part/p_aside_adminhrd.php"); ?>
        
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    
                    <!-- Header -->
                    <div class="row mb-3 mt-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h4 class="fw-bold mb-0">
                                        <i class="bi bi-people-fill text-primary me-2"></i>Data User
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tambah User -->
                    <div class="mb-3 text-end">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegister">
                            <i class="bi bi-plus-circle"></i> Tambah User Baru
                        </button>
                    </div>
                    
                    <!-- Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="datatablenya" class="table table-hover table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="3%"><center>No</center></th>
                                                    <th><center>Username</center></th>
                                                    <th><center>Nama Lengkap</center></th>
                                                    <th><center>NIK</center></th>
                                                    <th><center>Bagian</center></th>
                                                    <th><center>Departement</center></th>
                                                    <th><center>Jabatan</center></th>
                                                    <th width="10%"><center>Aksi</center></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $no = 1;
                                                while ($user = mysqli_fetch_assoc($result_users)) { 
                                                ?>
                                                <tr>
                                                    <td><center><?= $no++ ?></center></td>
                                                    <td><?= $user['username'] ?></td>
                                                    <td><?= $user['nama_lngkp'] ?></td>
                                                    <td><?= $user['nik'] ?></td>
                                                    <td><?= $user['bagian'] ?></td>
                                                    <td><?= $user['departement'] ?></td>
                                                    <td><?= $user['jabatan'] ?></td>
                                                    <td>
                                                        <center>
                                                            <button class="btn btn-sm btn-warning" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editModal<?= $user['id'] ?>"
                                                                    title="Edit Data">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            
                                                            <button class="btn btn-sm btn-info" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editPasswordModal<?= $user['id'] ?>"
                                                                    title="Edit Password">
                                                                <i class="bi bi-key"></i>
                                                            </button>
                                                            
                                                            <a href="?delete=<?= $user['id'] ?>" 
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Yakin ingin menghapus user ini?')"
                                                            title="Hapus User">
                                                                <i class="bi bi-trash"></i>
                                                            </a>
                                                        </center>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal Edit -->
                                                <div class="modal fade" id="editModal<?= $user['id'] ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title fw-bold">Edit Data User</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id_user" value="<?= $user['id'] ?>">
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Username</label>
                                                                            <input type="text" class="form-control" name="username" 
                                                                                   value="<?= $user['username'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Nama Lengkap</label>
                                                                            <input type="text" class="form-control" name="nama_lengkap" 
                                                                                   value="<?= $user['nama_lngkp'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">NIK</label>
                                                                            <input type="text" class="form-control" name="nik" 
                                                                                   value="<?= $user['nik'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Bagian</label>
                                                                            <input type="text" class="form-control" name="bagian" 
                                                                                   value="<?= $user['bagian'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Departement</label>
                                                                            <input type="text" class="form-control" name="departement" 
                                                                                   value="<?= $user['departement'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Jabatan</label>
                                                                            <input type="text" class="form-control" name="jabatan" 
                                                                                   value="<?= $user['jabatan'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Atasan</label>
                                                                            <input type="text" class="form-control" name="atasan" 
                                                                                   value="<?= $user['atasan'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Penilai</label>
                                                                            <input type="text" class="form-control" name="penilai" 
                                                                                   value="<?= $user['penilai'] ?>" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="edit_user" class="btn btn-primary">Simpan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal Edit Password -->
                                                <div class="modal fade" id="editPasswordModal<?= $user['id'] ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-warning text-white">
                                                                <h5 class="modal-title fw-bold">
                                                                    <i class="bi bi-key-fill me-2"></i>Edit Password
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id_user_pass" value="<?= $user['id'] ?>">
                                                                    
                                                                    <div class="alert alert-info">
                                                                        <i class="bi bi-info-circle me-2"></i>
                                                                        Edit password untuk: <strong><?= $user['nama_lngkp'] ?></strong>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Password Baru</label>
                                                                        <input type="password" class="form-control" name="new_password" 
                                                                            placeholder="Masukkan password baru" required>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Konfirmasi Password</label>
                                                                        <input type="password" class="form-control" name="confirm_password" 
                                                                            placeholder="Ulangi password baru" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                        <i class="bi bi-x-circle me-1"></i>Batal
                                                                    </button>
                                                                    <button type="submit" name="edit_password" class="btn btn-warning">
                                                                        <i class="bi bi-save me-1"></i>Simpan Password
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <?php include("pages/part/p_footer.php"); ?>
        <?php include("pages/adminhrd/register-adminhrd.php"); ?>
    </div>
</body>
</html>