<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';

// Koneksi ke database simulasi

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
              WHERE u.username != 'itboy'
              ORDER BY u.nama_lngkp ASC";
$result_users = mysqli_query($conn, $sql_users);

// Handle Delete
if (isset($_GET['delete'])) {
    $id_delete = mysqli_real_escape_string($conn, $_GET['delete']);
    
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
    $id_edit = mysqli_real_escape_string($conn, $_POST['id_user']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $bagian = mysqli_real_escape_string($conn, $_POST['bagian']);
    $departement = mysqli_real_escape_string($conn, $_POST['departement']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $atasan = mysqli_real_escape_string($conn, $_POST['atasan']);
    $penilai = mysqli_real_escape_string($conn, $_POST['penilai']);
    
    // Tentukan level berdasarkan jabatan
    $level = 1; // Default Karyawan
    if ($jabatan == 'Koordinator') {
        $level = 2;
    } elseif ($jabatan == 'Manager') {
        $level = 3;
    } elseif ($jabatan == 'Kadep') {
        $level = 4;
    } elseif ($jabatan == 'Direktur') {
        $level = 5;
    }
    
    // Update tb_users
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
    
    // Update tb_auth (update level juga)
    $sql_update_level = "UPDATE tb_auth SET level = '$level' WHERE id_user = '$id_edit'";
    
    if (mysqli_query($conn, $sql_update) && mysqli_query($conn, $sql_update_level)) {
        echo "<script>alert('Data user dan level berhasil diupdate'); window.location.href='datauser-adminhrd';</script>";
    } else {
        echo "<script>alert('Gagal update data user');</script>";
    }
}

if (isset($_POST['edit_password'])) {
    $id_edit_pass = mysqli_real_escape_string($conn, $_POST['id_user_pass']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
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
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $namalengkap = mysqli_real_escape_string($conn, $_POST['namalengkap']);
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $departemen = mysqli_real_escape_string($conn, $_POST['departemen']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $bagian = mysqli_real_escape_string($conn, $_POST['bagian']);
    $atasan = mysqli_real_escape_string($conn, $_POST['atasan']);
    $penilai = mysqli_real_escape_string($conn, $_POST['penilai']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);
    
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
                if ($jabatan == 'Koordinator') {
                    $level = 2;
                 } elseif ($jabatan == 'Manager') {
                    $level = 3;
                } elseif ($jabatan == 'Kadep') {
                    $level = 4;
                } elseif ($jabatan == 'Direktur') {
                    $level = 5;
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
                echo "<script>alert('Gagal menambahkan user: " . mysqli_error($conn) . "');</script>";
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
                    
                    <!-- Header Page Card Gradient -->
                    <div class="card shadow-sm mt-4 mb-4 border-0 overflow-hidden">
                        <div class="card-body text-white rounded"
                            style="background: linear-gradient(135deg, #4e73df, #1cc88a);">

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                                <div>
                                    <h4 class="mb-1 fw-semibold">
                                        <i class="bi bi-people-fill text-info"></i>
                                        Data User
                                    </h4>
                                    <small class="opacity-75">
                                        Kelola Data Pengguna
                                    </small>
                                </div>

                                <div>
                                    <a href="dashboard-adminhrd" class="btn btn-light btn-sm shadow-sm">
                                        <i class="bi bi-arrow-left me-1"></i>
                                        Kembali ke Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tambah User -->
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <!-- Filter Departemen -->
                            <select id="filterDepartemen" class="form-select form-select-sm" style="width: auto;">
                                <option value="">Semua Departemen</option>
                                <?php
                                // Ambil list departemen
                                $sql_dept = "SELECT DISTINCT departement FROM tb_users WHERE username != 'itboy' ORDER BY departement ASC";
                                $result_dept = mysqli_query($conn, $sql_dept);
                                while($dept = mysqli_fetch_assoc($result_dept)) {
                                    echo "<option value='".$dept['departement']."'>".$dept['departement']."</option>";
                                }
                                ?>
                            </select>
                            
                            <!-- Filter Jabatan -->
                            <select id="filterJabatan" class="form-select form-select-sm" style="width: auto;">
                                <option value="">Semua Jabatan</option>
                                <option value="Direktur">Direktur</option>
                                <option value="Kadep">Kadep</option>
                                <option value="Manager">Manager</option>
                                <option value="Koordinator">Koordinator</option>
                                <option value="Karyawan">Karyawan</option>
                                <option value="Driver">Driver</option>
                            </select>
                            
                            <!-- Tombol Reset Filter -->
                            <button id="resetFilter" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </button>
                        </div>
                        
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
                                                    <th><center>Level</center></th>
                                                    <th width="10%"><center>Aksi</center></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $no = 1;
                                                while ($user = mysqli_fetch_assoc($result_users)) { 
                                                    // Tampilkan nama level
                                                    $level_name = '';
                                                    switch($user['level']) {
                                                        case 1: $level_name = 'Karyawan'; break;
                                                        case 2: $level_name = 'Koordinator'; break;
                                                        case 3: $level_name = 'Manager'; break;
                                                        case 4: $level_name = 'Kadep'; break;
                                                        case 5: $level_name = 'Direktur'; break;
                                                        case 6: $level_name = 'Admin HRD'; break;
                                                        default: $level_name = 'Unknown';
                                                    }
                                                ?>
                                                <tr>
                                                    <td><center><?= $no++ ?></center></td>
                                                    <td><?= $user['username'] ?></td>
                                                    <td><?= $user['nama_lngkp'] ?></td>
                                                    <td><?= $user['nik'] ?></td>
                                                    <td><?= $user['bagian'] ?></td>
                                                    <td><?= $user['departement'] ?></td>
                                                    <td><?= $user['jabatan'] ?></td>
                                                    <td><center><span class="badge bg-primary"><?= $level_name ?></span></center></td>
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
                                                
                                                <!-- Modal Edit - Ganti bagian modal di dalam while loop -->
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
                                                                    
                                                                    <div class="alert alert-info">
                                                                        <i class="bi bi-info-circle me-2"></i>
                                                                        <strong>Perhatian:</strong> Level akan otomatis berubah sesuai jabatan yang dipilih
                                                                    </div>
                                                                    
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
                                                                            <label class="form-label">Departement <span class="text-danger">*</span></label>
                                                                            <select class="form-select departemen-edit" name="departement" 
                                                                                    data-user-id="<?= $user['id'] ?>" required>
                                                                                <option value="">Pilih Departemen</option>
                                                                                <option value="Keuangan & Sales" <?= $user['departement'] == 'Keuangan & Sales' ? 'selected' : '' ?>>Keuangan & Sales</option>
                                                                                <option value="Purchasing" <?= $user['departement'] == 'Purchasing' ? 'selected' : '' ?>>Purchasing</option>
                                                                                <option value="IT" <?= $user['departement'] == 'IT' ? 'selected' : '' ?>>IT</option>
                                                                                <option value="HRD" <?= $user['departement'] == 'HRD' ? 'selected' : '' ?>>HRD</option>
                                                                                <option value="Logistik" <?= $user['departement'] == 'Logistik' ? 'selected' : '' ?>>Logistik</option>
                                                                                <option value="GA" <?= $user['departement'] == 'GA' ? 'selected' : '' ?>>GA</option>
                                                                                <option value="Unit Bisnis Seed" <?= $user['departement'] == 'Unit Bisnis Seed' ? 'selected' : '' ?>>Unit Bisnis Seed</option>
                                                                                <option value="Unit Bisnis CP" <?= $user['departement'] == 'Unit Bisnis CP' ? 'selected' : '' ?>>Unit Bisnis CP</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                                                            <select class="form-select jabatan-edit" name="jabatan" 
                                                                                    data-user-id="<?= $user['id'] ?>" required>
                                                                                <option value="">-- Pilih Jabatan --</option>
                                                                                <option value="Karyawan" <?= $user['jabatan'] == 'Karyawan' ? 'selected' : '' ?>>Karyawan (Level 1)</option>
                                                                                <option value="Koordinator" <?= $user['jabatan'] == 'Koordinator' ? 'selected' : '' ?>>Koordinator (Level 2)</option>
                                                                                <option value="Manager" <?= $user['jabatan'] == 'Manager' ? 'selected' : '' ?>>Manager (Level 3)</option>
                                                                                <option value="Kadep" <?= $user['jabatan'] == 'Kadep' ? 'selected' : '' ?>>Kadep (Level 4)</option>
                                                                                <option value="Direktur" <?= $user['jabatan'] == 'Direktur' ? 'selected' : '' ?>>Direktur (Level 5)</option>
                                                                            </select>
                                                                            <small class="text-muted">Level akan otomatis disesuaikan dengan jabatan</small>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Atasan <span class="text-danger">*</span></label>
                                                                            <select class="form-select atasan-edit" name="atasan" 
                                                                                    id="atasan_edit_<?= $user['id'] ?>" required>
                                                                                <option value="">Pilih Atasan</option>
                                                                                <option value="<?= $user['atasan'] ?>" selected><?= $user['atasan'] ?></option>
                                                                            </select>
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
    <!-- jQuery harus dimuat pertama -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Handle perubahan departemen pada modal edit
        document.querySelectorAll('.departemen-edit').forEach(function(departemenSelect) {
            departemenSelect.addEventListener('change', function() {
                const userId = this.getAttribute('data-user-id');
                const atasanSelect = document.getElementById('atasan_edit_' + userId);
                const jabatanSelect = document.querySelector(`.jabatan-edit[data-user-id="${userId}"]`);
                
                // PENTING: Cek dulu jabatannya, jika Manager/Kadep/Direktur jangan overwrite
                if (jabatanSelect.value === 'Koordinator' || jabatanSelect.value === 'Manager'|| jabatanSelect.value === 'Kadep' || jabatanSelect.value === 'Direktur') {
                    return; // Skip update atasan jika jabatan sudah Manager/Kadep/Direktur
                }
                
                let items = [];

                if (this.value === 'Keuangan & Sales') {
                    items = ["Pilih Atasan", "Ibnu Sutoro", "Evi Yulia Purnama Sari", "Ahmad Syaiti", "Iva Isti Farini"];
                }
                else if (this.value === 'IT') {
                    items = ["Pilih Atasan", "Wahyu Arif Prasetyo"];
                }  
                else if (this.value === 'Purchasing') {
                    items = ["Pilih Atasan", "Evi Yulia", "Heru Sucahyo"];
                } 
                else if (this.value === 'HRD') {
                    items = ["Pilih Atasan","Siwi Mardlatus Syafirah","Riza Dwi Fitrianingtyas"];
                }
                else if (this.value === 'Logistik') {
                    items = ["Pilih Atasan", "Fauzan", "Wildan Ma'ruf N. W."];
                } 
                else if (this.value === 'GA') {
                    items = ["Pilih Atasan", "Nandang", "Wawan"];
                }
                else if (this.value === 'Unit Bisnis Seed') {
                    items = ["Pilih Atasan", "Acep Andriyanto", "Yama Muhammad", "Ahmad Muhlisin"];
                }
                else if (this.value === 'Unit Bisnis CP') {
                    items = ["Pilih Atasan", "Arfin Indra Cahyadi"];
                }

                renderAtasanEdit(items, atasanSelect);
            });
        });

        // Handle perubahan jabatan pada modal edit
        document.querySelectorAll('.jabatan-edit').forEach(function(jabatanSelect) {
            jabatanSelect.addEventListener('change', function() {
                const userId = this.getAttribute('data-user-id');
                const atasanSelect = document.getElementById('atasan_edit_' + userId);
                let items = [];

                if (this.value === 'Koordinator') {
                    items = ["Pilih Atasan", "Diana Wulandari", "Vita Ari Puspita", "Riza Dwi Fitrianingtyas", "Kurniawan Pratama Arifin", "Heru Sucahyo", "Arfin Indra Cahyadi"];
                    renderAtasanEdit(items, atasanSelect); // Update atasan untuk Manager
                } 
                else if (this.value === 'Manager') {
                    items = ["Pilih Atasan", "Diana Wulandari", "Vita Ari Puspita", "Riza Dwi Fitrianingtyas", "Kurniawan Pratama Arifin", "Heru Sucahyo", "Arfin Indra Cahyadi"];
                    renderAtasanEdit(items, atasanSelect); // Update atasan untuk Kadep
                } 
                else if (this.value === 'Kadep') {
                    items = ["Pilih Atasan", "Diana Wulandari"];
                    renderAtasanEdit(items, atasanSelect); // Update atasan untuk Kadep
                } 
                else if (this.value === 'Direktur') {
                    items = ["Pilih Atasan", "Direksi"];
                    renderAtasanEdit(items, atasanSelect); // Update atasan untuk Direktur
                }
                else if (this.value === 'Karyawan') {
                    // Trigger change event pada departemen untuk update atasan karyawan
                    const departemenSelect = document.querySelector(`.departemen-edit[data-user-id="${userId}"]`);
                    if (departemenSelect && departemenSelect.value) {
                        departemenSelect.dispatchEvent(new Event('change'));
                    }
                }
            });
        });

        // Function untuk render dropdown atasan di modal edit
        function renderAtasanEdit(items, selectElement) {
            let str = "";
            for (let item of items) {
                str += `<option value="${item}">${item}</option>`;
            }
            selectElement.innerHTML = str;
        }
    });

        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#datatablenya').DataTable({
                "responsive": true,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    },
                    "zeroRecords": "Data tidak ditemukan"
                },
                "pageLength": 10,
                "order": [[2, 'asc']], // Order by nama lengkap
                "columnDefs": [
                    { "orderable": false, "targets": 8 } // Kolom aksi tidak bisa diurutkan
                ]
            });
            
            // Filter Departemen
            $('#filterDepartemen').on('change', function() {
                var dept = $(this).val();
                table.column(5).search(dept).draw(); // Kolom 5 = Departement
            });
            
            // Filter Jabatan
            $('#filterJabatan').on('change', function() {
                var jabatan = $(this).val();
                table.column(6).search(jabatan).draw(); // Kolom 6 = Jabatan
            });
            
            // Reset Filter
            $('#resetFilter').on('click', function() {
                $('#filterDepartemen').val('');
                $('#filterJabatan').val('');
                table.search('').columns().search('').draw();
            });
        });
    </script>
    
</body>
</html>