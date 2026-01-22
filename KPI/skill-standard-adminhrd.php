<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {
    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/checkAdmin.php';

    // Hanya Admin HRD yang bisa akses
    requireAdminHRD();
    
    // ========== HANDLER UNTUK SKILL STANDARD ==========
    
    // MENJADI (BENAR):
    if (isset($_POST['tambah_skill'])) {
        $id_user_skill = intval($_POST['id_user']);
        $nama_skill = mysqli_real_escape_string($conn, $_POST['nama_skill']);
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $created_by = $_SESSION['id_user'];
        
        // 1. Cek apakah kategori sudah ada untuk user ini
        $check_kategori = "SELECT id_poinss FROM tb_ss WHERE id_user = ? AND poin_ss = ?";
        $stmt_check = mysqli_prepare($conn, $check_kategori);
        mysqli_stmt_bind_param($stmt_check, "is", $id_user_skill, $kategori);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        
        if (mysqli_num_rows($result_check) > 0) {
            // Kategori sudah ada, ambil id-nya
            $row = mysqli_fetch_assoc($result_check);
            $id_ss = $row['id_poinss'];
        } else {
            // Kategori belum ada, buat baru
            $insert_kategori = "INSERT INTO tb_ss (id_user, poin_ss) VALUES (?, ?)";
            $stmt_kategori = mysqli_prepare($conn, $insert_kategori);
            mysqli_stmt_bind_param($stmt_kategori, "is", $id_user_skill, $kategori);
            mysqli_stmt_execute($stmt_kategori);
            $id_ss = mysqli_insert_id($conn);
        }
        
        // 2. Insert skill ke tb_sspoin
        $nilai1 = mysqli_real_escape_string($conn, $_POST['indikator_1'] ?? '');
        $nilai2 = mysqli_real_escape_string($conn, $_POST['indikator_2'] ?? '');
        $nilai3 = mysqli_real_escape_string($conn, $_POST['indikator_3'] ?? '');
        $nilai4 = mysqli_real_escape_string($conn, $_POST['indikator_4'] ?? '');
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi'] ?? '');
        $nilai = floatval($_POST['level'] ?? 0); // Ubah dari text level ke numeric
        
        $sql = "INSERT INTO tb_sspoin (id_user, id_ss, poinss, nilai1, nilai2, nilai3, nilai4, nilaiss, deskripsi) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iisssssds", 
            $id_user_skill, $id_ss, $nama_skill, 
            $nilai1, $nilai2, $nilai3, $nilai4, $nilai, $deskripsi);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Skill Standard berhasil ditambahkan!'); window.location.href='skill-standard-adminhrd';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan Skill Standard: " . mysqli_error($conn) . "');</script>";
        }
    }
    
    // Handler untuk edit skill standard
    // MENJADI (BENAR):
if (isset($_POST['edit_skill'])) {
    $id_skill = intval($_POST['id_skill']);
    $nama_skill = mysqli_real_escape_string($conn, $_POST['nama_skill']);
    $nilai1 = mysqli_real_escape_string($conn, $_POST['indikator_1'] ?? '');
    $nilai2 = mysqli_real_escape_string($conn, $_POST['indikator_2'] ?? '');
    $nilai3 = mysqli_real_escape_string($conn, $_POST['indikator_3'] ?? '');
    $nilai4 = mysqli_real_escape_string($conn, $_POST['indikator_4'] ?? '');
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi'] ?? '');
    $nilai = floatval($_POST['level'] ?? 0);
    
    $sql = "UPDATE tb_sspoin 
            SET poinss = ?, nilai1 = ?, nilai2 = ?, nilai3 = ?, nilai4 = ?, nilaiss = ?, deskripsi = ? 
            WHERE id_sspoin = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssdi", 
        $nama_skill, $nilai1, $nilai2, $nilai3, $nilai4, $nilai, $deskripsi, $id_skill);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Skill Standard berhasil diupdate!'); window.location.href='skill-standard-adminhrd';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate Skill Standard!');</script>";
    }
}
    
    // DARI (SALAH):
if (isset($_POST['hapus_skill'])) {
    $id_skill = intval($_POST['id_skill']);
    $sql = "DELETE FROM tb_skill_standard WHERE id_skill = ?";
}

// MENJADI (BENAR):
if (isset($_POST['hapus_skill'])) {
    $id_skill = intval($_POST['id_skill']);
    $sql = "DELETE FROM tb_sspoin WHERE id_sspoin = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_skill);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Skill Standard berhasil dihapus!'); window.location.href='skill-standard-adminhrd';</script>";
    } else {
        echo "<script>alert('Gagal menghapus Skill Standard!');</script>";
    }
}
    
    // Ambil data semua karyawan untuk dropdown
    $sql_users = "SELECT u.id, u.nama_lngkp, u.jabatan, u.divisi 
                  FROM tb_users u
                  INNER JOIN tb_auth a ON u.id = a.id_user
                  WHERE u.jabatan != 'Admin HRD'
                  ORDER BY u.nama_lngkp ASC";
    $result_users = mysqli_query($conn, $sql_users);
    
    // MENJADI (BENAR - sesuai struktur database):
$sql_skills = "SELECT 
                sp.id_sspoin,
                sp.id_user,
                sp.poinss as nama_skill,
                ss.poin_ss as kategori,
                sp.nilaiss as level,
                sp.deskripsi,
                sp.nilai1,
                sp.nilai2,
                sp.nilai3,
                sp.nilai4,
                u.nama_lngkp,
                u.jabatan,
                u.divisi,
                creator.nama_lngkp as created_by_name
                FROM tb_sspoin sp
                INNER JOIN tb_ss ss ON sp.id_ss = ss.id_poinss
                INNER JOIN tb_users u ON sp.id_user = u.id
                LEFT JOIN tb_users creator ON ss.id_user = creator.id
                ORDER BY sp.id_sspoin DESC";
    $result_skills = mysqli_query($conn, $sql_skills);
    
    // MENJADI (BENAR):
$sql_stats = "SELECT 
                COUNT(*) as total_skills,
                COUNT(DISTINCT id_user) as total_karyawan_with_skills,
                SUM(CASE WHEN nilaiss >= 3.5 THEN 1 ELSE 0 END) as total_expert,
                SUM(CASE WHEN nilaiss >= 2.5 AND nilaiss < 3.5 THEN 1 ELSE 0 END) as total_advanced,
                SUM(CASE WHEN nilaiss >= 1.5 AND nilaiss < 2.5 THEN 1 ELSE 0 END) as total_intermediate,
                SUM(CASE WHEN nilaiss > 0 AND nilaiss < 1.5 THEN 1 ELSE 0 END) as total_beginner
                FROM tb_sspoin";
    $result_stats = mysqli_query($conn, $sql_stats);
    $stats = mysqli_fetch_assoc($result_stats);
}
?>

<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Skill Standard - Admin HRD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/datatables/datatables.min.css" />
    
    <style>
        .header-skill {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
        }
        
        .stat-card {
            border-radius: 12px;
            transition: all 0.3s ease;
            border: none;
            background: white;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
        }
        
        .badge-level {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .level-expert {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
        }
        
        .level-advanced {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .level-intermediate {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        
        .level-beginner {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .badge-kategori {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }
        
        .btn-action {
            padding: 6px 12px;
            margin: 0 2px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
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
                    <div class="header-skill mt-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-2 fw-bold">
                                    <i class="bi bi-award-fill me-2"></i>Skill Standard Karyawan
                                </h3>
                                <p class="mb-0 opacity-90">Kelola dan Monitor Standar Kompetensi Seluruh Karyawan</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <button class="btn btn-light btn-lg shadow" data-toggle="modal" data-target="#modalTambahSkill">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Skill
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card stat-card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1 small">Total Skills</h6>
                                            <h3 class="fw-bold mb-0 text-primary"><?= $stats['total_skills'] ?></h3>
                                            <small class="text-muted">Skill tercatat</small>
                                        </div>
                                        <div class="text-primary" style="opacity: 0.8;">
                                            <i class="bi bi-clipboard-check-fill" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card stat-card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1 small">Expert Level</h6>
                                            <h3 class="fw-bold mb-0 text-danger"><?= $stats['total_expert'] ?></h3>
                                            <small class="text-muted">Tingkat ahli</small>
                                        </div>
                                        <div class="text-danger" style="opacity: 0.8;">
                                            <i class="bi bi-trophy-fill" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card stat-card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1 small">Advanced Level</h6>
                                            <h3 class="fw-bold mb-0 text-warning"><?= $stats['total_advanced'] ?></h3>
                                            <small class="text-muted">Tingkat lanjut</small>
                                        </div>
                                        <div class="text-warning" style="opacity: 0.8;">
                                            <i class="bi bi-star-fill" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card stat-card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1 small">Karyawan</h6>
                                            <h3 class="fw-bold mb-0 text-success"><?= $stats['total_karyawan_with_skills'] ?></h3>
                                            <small class="text-muted">Memiliki skill</small>
                                        </div>
                                        <div class="text-success" style="opacity: 0.8;">
                                            <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Data Skill Standard -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                                <div class="card-header bg-white border-0 pt-4">
                                    <h5 class="fw-bold mb-0">
                                        <i class="bi bi-table me-2 text-primary"></i>Data Skill Standard
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tableSkill" class="table table-hover table-striped">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th class="text-center" width="5%">No</th>
                                                    <th width="15%">Nama Karyawan</th>
                                                    <th width="12%">Jabatan</th>
                                                    <th width="12%">Divisi</th>
                                                    <th width="15%">Nama Skill</th>
                                                    <th width="10%">Kategori</th>
                                                    <th class="text-center" width="8%">Level</th>
                                                    <th width="12%">Tanggal Penilaian</th>
                                                    <th class="text-center" width="11%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $no = 1;
                                                while($row = mysqli_fetch_assoc($result_skills)): 
                                                    // Tentukan class level
                                                    $level_class = '';
                                                    switch($row['level']) {
                                                        case 'Expert':
                                                            $level_class = 'level-expert';
                                                            break;
                                                        case 'Advanced':
                                                            $level_class = 'level-advanced';
                                                            break;
                                                        case 'Intermediate':
                                                            $level_class = 'level-intermediate';
                                                            break;
                                                        case 'Beginner':
                                                            $level_class = 'level-beginner';
                                                            break;
                                                    }
                                                    
                                                    // Warna kategori
                                                    $kategori_colors = [
                                                        'Technical' => 'bg-primary text-white',
                                                        'Soft Skill' => 'bg-success text-white',
                                                        'Leadership' => 'bg-danger text-white',
                                                        'Management' => 'bg-warning text-dark',
                                                        'Communication' => 'bg-info text-white',
                                                        'Others' => 'bg-secondary text-white'
                                                    ];
                                                    $kategori_class = $kategori_colors[$row['kategori']] ?? 'bg-secondary text-white';
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++ ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($row['nama_lngkp']) ?></strong>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                                    <td><?= htmlspecialchars($row['divisi']) ?></td>
                                                    <td>
                                                        <strong class="text-primary"><?= htmlspecialchars($row['nama_skill']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-kategori <?= $kategori_class ?>">
                                                            <?= htmlspecialchars($row['kategori']) ?>
                                                        </span>
                                                    </td>
                                                    // MENJADI (display nilai numeric):
<td class="text-center">
    <?php 
    $nilai = floatval($row['level']);
    if ($nilai >= 3.5) {
        $level_class = 'level-expert';
        $level_text = 'Expert (' . number_format($nilai, 2) . ')';
    } elseif ($nilai >= 2.5) {
        $level_class = 'level-advanced';
        $level_text = 'Advanced (' . number_format($nilai, 2) . ')';
    } elseif ($nilai >= 1.5) {
        $level_class = 'level-intermediate';
        $level_text = 'Intermediate (' . number_format($nilai, 2) . ')';
    } elseif ($nilai > 0) {
        $level_class = 'level-beginner';
        $level_text = 'Beginner (' . number_format($nilai, 2) . ')';
    } else {
        $level_class = 'bg-secondary text-white';
        $level_text = 'Belum Dinilai';
    }
    ?>
    <span class="badge badge-level <?= $level_class ?>">
        <?= $level_text ?>
    </span>
</td>
                                                    <td><?= date('d/m/Y', strtotime($row['tanggal_penilaian'])) ?></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-info btn-action" 
                                                                onclick="viewDetail(<?= htmlspecialchars(json_encode($row)) ?>)"
                                                                title="Detail">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-warning btn-action" 
                                                                onclick="editSkill(<?= htmlspecialchars(json_encode($row)) ?>)"
                                                                title="Edit">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger btn-action" 
                                                                onclick="hapusSkill(<?= $row['id_skill'] ?>, '<?= htmlspecialchars($row['nama_skill']) ?>')"
                                                                title="Hapus">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
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
        
        <?php include("pages/part/p_footeradminhrd.php"); ?>
    </div>

    <!-- Modal Tambah Skill -->
    <div class="modal fade" id="modalTambahSkill" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Skill Standard
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">Nama Karyawan <span class="text-danger">*</span></label>
                                    <select name="id_user" class="form-control" required>
                                        <option value="">-- Pilih Karyawan --</option>
                                        <?php 
                                        mysqli_data_seek($result_users, 0);
                                        while($user = mysqli_fetch_assoc($result_users)): 
                                        ?>
                                        <option value="<?= $user['id'] ?>">
                                            <?= htmlspecialchars($user['nama_lngkp']) ?> - <?= htmlspecialchars($user['jabatan']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">Nama Skill <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_skill" class="form-control" 
                                           placeholder="Contoh: Microsoft Excel, Leadership" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">Kategori <span class="text-danger">*</span></label>
                                    <select name="kategori" class="form-control" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Technical">Technical</option>
                                        <option value="Soft Skill">Soft Skill</option>
                                        <option value="Leadership">Leadership</option>
                                        <option value="Management">Management</option>
                                        <option value="Communication">Communication</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">Level <span class="text-danger">*</span></label>
                                    <input type="number" name="level" class="form-control" 
       min="0" max="4" step="0.1"
       placeholder="Masukkan nilai 0-4" required>
<small class="text-muted">0-1.5: Beginner | 1.5-2.5: Intermediate | 2.5-3.5: Advanced | 3.5-4: Expert</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="fw-bold">Tanggal Penilaian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_penilaian" class="form-control" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="fw-bold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" 
                                      placeholder="Deskripsi kemampuan atau catatan penilaian"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </button>
                        <button type="submit" name="edit_skill" class="btn btn-warning">
                            <i class="bi bi-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Skill -->
    <div class="modal fade" id="modalDetailSkill" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-eye-fill me-2"></i>Detail Skill Standard
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Nama Karyawan</label>
                            <h6 class="fw-bold" id="detail_nama_karyawan"></h6>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small mb-1">Jabatan</label>
                            <h6 id="detail_jabatan"></h6>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small mb-1">Divisi</label>
                            <h6 id="detail_divisi"></h6>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Nama Skill</label>
                            <h5 class="fw-bold text-primary" id="detail_nama_skill"></h5>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small mb-1">Kategori</label>
                            <h6><span id="detail_kategori_badge"></span></h6>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small mb-1">Level</label>
                            <h6><span id="detail_level_badge"></span></h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Tanggal Penilaian</label>
                            <h6 id="detail_tanggal"></h6>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Dinilai Oleh</label>
                            <h6 id="detail_created_by"></h6>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small mb-1">Deskripsi</label>
                        <div class="p-3 bg-light rounded">
                            <p id="detail_deskripsi" class="mb-0"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Hidden untuk Hapus -->
    <form id="formHapusSkill" method="POST" style="display:none;">
        <input type="hidden" name="id_skill" id="hapus_id_skill">
        <input type="hidden" name="hapus_skill" value="1">
    </form>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/datatables/datatables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#tableSkill').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "pageLength": 25,
                "order": [[7, "desc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [8] }
                ]
            });
        });

        function editSkill(data) {
            $('#edit_id_skill').val(data.id_skill);
            $('#edit_nama_karyawan').val(data.nama_lngkp + ' - ' + data.jabatan);
            $('#edit_nama_skill').val(data.nama_skill);
            $('#edit_kategori').val(data.kategori);
            $('#edit_level').val(data.level);
            $('#edit_tanggal').val(data.tanggal_penilaian);
            $('#edit_deskripsi').val(data.deskripsi);
            $('#modalEditSkill').modal('show');
        }

        function viewDetail(data) {
            $('#detail_nama_karyawan').text(data.nama_lngkp);
            $('#detail_jabatan').text(data.jabatan);
            $('#detail_divisi').text(data.divisi);
            $('#detail_nama_skill').text(data.nama_skill);
            $('#detail_tanggal').text(formatDate(data.tanggal_penilaian));
            $('#detail_created_by').text(data.created_by_name || 'System');
            $('#detail_deskripsi').text(data.deskripsi || 'Tidak ada deskripsi');

            // Kategori badge
            const kategoriColors = {
                'Technical': 'bg-primary text-white',
                'Soft Skill': 'bg-success text-white',
                'Leadership': 'bg-danger text-white',
                'Management': 'bg-warning text-dark',
                'Communication': 'bg-info text-white',
                'Others': 'bg-secondary text-white'
            };
            const kategoriClass = kategoriColors[data.kategori] || 'bg-secondary text-white';
            $('#detail_kategori_badge').html('<span class="badge badge-kategori ' + kategoriClass + '">' + data.kategori + '</span>');

            // Level badge
            const levelClass = {
                'Expert': 'level-expert',
                'Advanced': 'level-advanced',
                'Intermediate': 'level-intermediate',
                'Beginner': 'level-beginner'
            }[data.level] || 'level-beginner';
            $('#detail_level_badge').html('<span class="badge badge-level ' + levelClass + '">' + data.level + '</span>');

            $('#modalDetailSkill').modal('show');
        }

        function hapusSkill(id, nama) {
            if (confirm('Apakah Anda yakin ingin menghapus skill "' + nama + '"?')) {
                $('#hapus_id_skill').val(id);
                $('#formHapusSkill').submit();
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }
    </script>
</body>
</html>