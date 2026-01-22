<?php
// File: kpi-lock-settings-adminhrd.php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';
require 'helper/checkAdmin.php';
require 'helper/kpi_lock_functions.php';

// Hanya Admin HRD yang bisa akses
requireAdminHRD();

// Handler untuk tambah periode lock
if (isset($_POST['tambah_lock'])) {
    $nama_periode = mysqli_real_escape_string($conn, $_POST['nama_periode']);
    $tanggal_mulai = mysqli_real_escape_string($conn, $_POST['tanggal_mulai']);
    $tanggal_selesai = mysqli_real_escape_string($conn, $_POST['tanggal_selesai']);
    $level_akses = isset($_POST['level_akses']) ? implode(',', $_POST['level_akses']) : '';
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    // Izin akses
    $izin_akses = [
        'view' => isset($_POST['izin_view']),
        'add' => isset($_POST['izin_add']),
        'edit' => isset($_POST['izin_edit']),
        'delete' => isset($_POST['izin_delete'])
    ];
    $izin_akses_json = json_encode($izin_akses);
    
    // Validasi tanggal
    if ($tanggal_selesai < $tanggal_mulai) {
        echo "<script>alert('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai!');</script>";
    } elseif (checkPeriodOverlap($conn, $tanggal_mulai, $tanggal_selesai)) {
        echo "<script>alert('Periode yang dipilih bertumpuk dengan periode lain yang sudah ada!');</script>";
    } else {
        $sql = "INSERT INTO tb_kpi_lock_settings 
                (nama_periode, tanggal_mulai, tanggal_selesai, level_akses, izin_akses, keterangan, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssi", $nama_periode, $tanggal_mulai, $tanggal_selesai, 
                              $level_akses, $izin_akses_json, $keterangan, $id_user);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Pengaturan lock berhasil ditambahkan!'); window.location.href='kpi-lock-settings-adminhrd';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan pengaturan lock!');</script>";
        }
    }
}

// Handler untuk edit periode lock
if (isset($_POST['edit_lock'])) {
    $id_lock = intval($_POST['id_lock']);
    $nama_periode = mysqli_real_escape_string($conn, $_POST['nama_periode']);
    $tanggal_mulai = mysqli_real_escape_string($conn, $_POST['tanggal_mulai']);
    $tanggal_selesai = mysqli_real_escape_string($conn, $_POST['tanggal_selesai']);
    $level_akses = isset($_POST['level_akses']) ? implode(',', $_POST['level_akses']) : '';
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    $izin_akses = [
        'view' => isset($_POST['izin_view']),
        'add' => isset($_POST['izin_add']),
        'edit' => isset($_POST['izin_edit']),
        'delete' => isset($_POST['izin_delete'])
    ];
    $izin_akses_json = json_encode($izin_akses);
    
    if ($tanggal_selesai < $tanggal_mulai) {
        echo "<script>alert('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai!');</script>";
    } elseif (checkPeriodOverlap($conn, $tanggal_mulai, $tanggal_selesai, $id_lock)) {
        echo "<script>alert('Periode yang dipilih bertumpuk dengan periode lain yang sudah ada!');</script>";
    } else {
        $sql = "UPDATE tb_kpi_lock_settings SET 
                nama_periode = ?, tanggal_mulai = ?, tanggal_selesai = ?, 
                level_akses = ?, izin_akses = ?, keterangan = ?
                WHERE id_lock = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssi", $nama_periode, $tanggal_mulai, $tanggal_selesai, 
                              $level_akses, $izin_akses_json, $keterangan, $id_lock);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Pengaturan lock berhasil diupdate!'); window.location.href='kpi-lock-settings-adminhrd';</script>";
        } else {
            echo "<script>alert('Gagal mengupdate pengaturan lock!');</script>";
        }
    }
}

// Handler untuk hapus/nonaktifkan periode lock
if (isset($_POST['hapus_lock'])) {
    $id_lock = intval($_POST['id_lock']);
    
    $sql = "UPDATE tb_kpi_lock_settings SET status = 'nonaktif' WHERE id_lock = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_lock);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Pengaturan lock berhasil dihapus!'); window.location.href='kpi-lock-settings-adminhrd';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pengaturan lock!');</script>";
    }
}

// Ambil semua periode lock aktif
$result_locks = getAllActiveLockPeriods($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Lock KPI - Admin HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/datatables/datatables.min.css" />
    <link rel="stylesheet" href="assets/css/adminlte.css">
    
    <style>
        .badge-level {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            margin-right: 3px;
        }
        .periode-card {
            border-left: 4px solid #007bff;
            transition: all 0.3s;
        }
        .periode-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .periode-aktif {
            border-left-color: #28a745 !important;
            background-color: #f8fff9;
        }
        .periode-akan-datang {
            border-left-color: #ffc107 !important;
        }
        .periode-selesai {
            border-left-color: #6c757d !important;
            opacity: 0.7;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_adminhrd.php"); ?>
        <?php include("pages/part/p_aside_adminhrd.php"); ?>
        
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    
                    <!-- Header -->
                    <div class="row mt-4 mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-2">
                                                <i class="bi bi-lock-fill me-2"></i>Pengaturan Lock Akses KPI
                                            </h4>
                                            <p class="mb-0 opacity-75">Atur periode dan level akses untuk pengisian KPI karyawan</p>
                                        </div>
                                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalTambahLock">
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Periode Lock
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <strong>Cara Kerja:</strong> 
                                Atur periode waktu dan level jabatan mana saja yang boleh mengakses KPI. 
                                Level: 1=Karyawan, 2=Koordinator, 3=Manager, 4=Kadep. 
                                Admin HRD selalu memiliki akses penuh.
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Periode Lock -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Daftar Periode Lock</h5>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    $today = date('Y-m-d');
                                    if (mysqli_num_rows($result_locks) > 0): 
                                    ?>
                                        <div class="row">
                                            <?php while($lock = mysqli_fetch_assoc($result_locks)): 
                                                $izin = json_decode($lock['izin_akses'], true);
                                                $is_aktif = ($lock['tanggal_mulai'] <= $today && $lock['tanggal_selesai'] >= $today);
                                                $is_akan_datang = ($lock['tanggal_mulai'] > $today);
                                                $is_selesai = ($lock['tanggal_selesai'] < $today);
                                                
                                                $card_class = 'periode-card';
                                                if ($is_aktif) $card_class .= ' periode-aktif';
                                                elseif ($is_akan_datang) $card_class .= ' periode-akan-datang';
                                                elseif ($is_selesai) $card_class .= ' periode-selesai';
                                            ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="card <?=$card_class?>">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h5 class="card-title mb-0">
                                                                <?=$lock['nama_periode']?>
                                                                <?php if($is_aktif): ?>
                                                                    <span class="badge bg-success ms-2">AKTIF SEKARANG</span>
                                                                <?php elseif($is_akan_datang): ?>
                                                                    <span class="badge bg-warning ms-2">AKAN DATANG</span>
                                                                <?php elseif($is_selesai): ?>
                                                                    <span class="badge bg-secondary ms-2">SELESAI</span>
                                                                <?php endif; ?>
                                                            </h5>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                                    <i class="bi bi-three-dots-vertical"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a class="dropdown-item" href="#" 
                                                                           onclick="editLock(<?=htmlspecialchars(json_encode($lock))?>)">
                                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item text-danger" href="#"
                                                                           onclick="hapusLock(<?=$lock['id_lock']?>, '<?=$lock['nama_periode']?>')">
                                                                            <i class="bi bi-trash me-2"></i>Hapus
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <i class="bi bi-calendar-range text-muted me-2"></i>
                                                            <strong>Periode:</strong>
                                                            <?=date('d/m/Y', strtotime($lock['tanggal_mulai']))?> - 
                                                            <?=date('d/m/Y', strtotime($lock['tanggal_selesai']))?>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <i class="bi bi-people text-muted me-2"></i>
                                                            <strong>Level Akses:</strong>
                                                            <?php if(empty($lock['level_akses'])): ?>
                                                                <span class="badge bg-danger">SEMUA DITUTUP</span>
                                                            <?php else:
                                                                $levels = explode(',', $lock['level_akses']);
                                                                $level_names = [
                                                                    '1' => 'Karyawan',
                                                                    '2' => 'Koordinator',
                                                                    '3' => 'Manager',
                                                                    '4' => 'Kadep'
                                                                ];
                                                                foreach($levels as $lv):
                                                            ?>
                                                                <span class="badge badge-level bg-primary"><?=$level_names[$lv]?></span>
                                                            <?php endforeach; endif; ?>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <i class="bi bi-shield-check text-muted me-2"></i>
                                                            <strong>Izin:</strong>
                                                            <?php if($izin['view']): ?>
                                                                <span class="badge bg-info">Lihat</span>
                                                            <?php endif; ?>
                                                            <?php if($izin['add']): ?>
                                                                <span class="badge bg-success">Tambah</span>
                                                            <?php endif; ?>
                                                            <?php if($izin['edit']): ?>
                                                                <span class="badge bg-warning">Edit</span>
                                                            <?php endif; ?>
                                                            <?php if($izin['delete']): ?>
                                                                <span class="badge bg-danger">Hapus</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <?php if(!empty($lock['keterangan'])): ?>
                                                        <div class="mt-2 pt-2 border-top">
                                                            <small class="text-muted">
                                                                <i class="bi bi-info-circle me-1"></i>
                                                                <?=$lock['keterangan']?>
                                                            </small>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endwhile; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-3">Belum ada pengaturan lock. Klik tombol "Tambah Periode Lock" untuk memulai.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
        
        <?php include("pages/part/p_footeradminhrd.php"); ?>
    </div>

    <!-- Modal Tambah Lock -->
    <div class="modal fade" id="modalTambahLock" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Periode Lock</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Nama Periode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_periode" required
                                       placeholder="Contoh: Periode Input Bulan Januari">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_mulai" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_selesai" required>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Level yang Boleh Akses</label>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Jika tidak ada yang dipilih, berarti <strong>SEMUA LEVEL DITUTUP</strong> (hanya bisa lihat)
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="level_akses[]" value="1" id="level1">
                                    <label class="form-check-label" for="level1">Level 1 - Karyawan</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="level_akses[]" value="2" id="level2">
                                    <label class="form-check-label" for="level2">Level 2 - Koordinator</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="level_akses[]" value="3" id="level3">
                                    <label class="form-check-label" for="level3">Level 3 - Manager</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="level_akses[]" value="4" id="level4">
                                    <label class="form-check-label" for="level4">Level 4 - Kadep</label>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Izin Akses untuk Level Terpilih</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="izin_view" value="1" id="izin_view" checked>
                                    <label class="form-check-label" for="izin_view">Boleh Lihat Data</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="izin_add" value="1" id="izin_add" checked>
                                    <label class="form-check-label" for="izin_add">Boleh Tambah Data</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="izin_edit" value="1" id="izin_edit" checked>
                                    <label class="form-check-label" for="izin_edit">Boleh Edit Data</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="izin_delete" value="1" id="izin_delete" checked>
                                    <label class="form-check-label" for="izin_delete">Boleh Hapus Data</label>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea class="form-control" name="keterangan" rows="3" 
                                          placeholder="Keterangan tambahan tentang periode ini..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_lock" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Lock -->
    <div class="modal fade" id="modalEditLock" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" id="formEditLock">
                    <input type="hidden" name="id_lock" id="edit_id_lock">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Periode Lock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Nama Periode</label>
                                <input type="text" class="form-control" name="nama_periode" id="edit_nama_periode" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai" id="edit_tanggal_mulai" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="tanggal_selesai" id="edit_tanggal_selesai" required>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Level yang Boleh Akses</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="level_akses[]" value="1" id="edit_level1">
                                    <label class="form-check-label" for="edit_level1">Level 1 - Karyawan</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="level_akses[]" value="2" id="edit_level2">
                                    <label class="form-check-label" for="edit_level2">Level 2 - Koordinator</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="level_akses[]" value="3" id="edit_level3">
                                    <label class="form-check-label" for="edit_level3">Level 3 - Manager</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="level_akses[]" value="4" id="edit_level4">
                                    <label class="form-check-label" for="edit_level4">Level 4 - Kadep</label>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Izin Akses</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="izin_view" value="1" id="edit_izin_view">
                                    <label class="form-check-label" for="edit_izin_view">Boleh Lihat Data</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="izin_add" value="1" id="edit_izin_add">
                                    <label class="form-check-label" for="edit_izin_add">Boleh Tambah Data</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="izin_edit" value="1" id="edit_izin_edit">
                                    <label class="form-check-label" for="edit_izin_edit">Boleh Edit Data</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="izin_delete" value="1" id="edit_izin_delete">
                                    <label class="form-check-label" for="edit_izin_delete">Boleh Hapus Data</label>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="edit_keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_lock" class="btn btn-warning">
                            <i class="bi bi-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Hapus (hidden) -->
    <form method="POST" id="formHapusLock" style="display:none;">
        <input type="hidden" name="id_lock" id="hapus_id_lock">
        <input type="hidden" name="hapus_lock" value="1">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function editLock(data) {
            // Populate form
            document.getElementById('edit_id_lock').value = data.id_lock;
            document.getElementById('edit_nama_periode').value = data.nama_periode;
            document.getElementById('edit_tanggal_mulai').value = data.tanggal_mulai;
            document.getElementById('edit_tanggal_selesai').value = data.tanggal_selesai;
            document.getElementById('edit_keterangan').value = data.keterangan;
            
            // Uncheck semua level dulu
            document.querySelectorAll('[id^="edit_level"]').forEach(cb => cb.checked = false);
            
            // Check level yang sesuai
            if (data.level_akses) {
                const levels = data.level_akses.split(',');
                levels.forEach(level => {
                    const checkbox = document.getElementById('edit_level' + level);
                    if (checkbox) checkbox.checked = true;
                });
            }
            
            // Set izin akses
            const izin = JSON.parse(data.izin_akses);
            document.getElementById('edit_izin_view').checked = izin.view || false;
            document.getElementById('edit_izin_add').checked = izin.add || false;
            document.getElementById('edit_izin_edit').checked = izin.edit || false;
            document.getElementById('edit_izin_delete').checked = izin.delete || false;
            
            // Show modal
            new bootstrap.Modal(document.getElementById('modalEditLock')).show();
        }
        
        function hapusLock(id, nama) {
            if (confirm('Yakin ingin menghapus periode "' + nama + '"?')) {
                document.getElementById('hapus_id_lock').value = id;
                document.getElementById('formHapusLock').submit();
            }
        }
    </script>
</body>
</html>