<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';

// Handle Delete SOP
if (isset($_GET['delete_sop'])) {
    $id_del = (int)$_GET['delete_sop'];
    $q_file = mysqli_query($conn, "SELECT namafile_sop FROM tb_sop WHERE id_sop = $id_del");
    if ($f_data = mysqli_fetch_assoc($q_file)) {
        $filepath = "assets/pdf/sop/" . $f_data['namafile_sop'];
        if (file_exists($filepath)) {
            @unlink($filepath);
        }
    }
    mysqli_query($conn, "DELETE FROM tb_sop WHERE id_sop = $id_del");
    echo "<script>alert('SOP berhasil dihapus.'); window.location.href='updatesop';</script>";
    exit();
}

// Handle Upload SOP
if (isset($_POST['submitsop']) && !empty($_FILES['file']['name'])) {
    $targetdir = "assets/pdf/sop/";
    if (!is_dir($targetdir)) {
        mkdir($targetdir, 0777, true);
    }

    $user_dept = !empty($departement) ? $departement : (!empty($bagian) ? $bagian : 'Umum');
    $nama  = mysqli_real_escape_string($conn, trim($_POST['namasop']));
    $kode  = mysqli_real_escape_string($conn, trim($_POST['kodesop']));
    $tipe  = mysqli_real_escape_string($conn, trim($user_dept));
    $iska  = isset($_POST['inlineCheckbox1']) ? 1 : 0;
    $ispri = isset($_POST['inlineCheckbox2']) ? 1 : 0;

    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        echo "<script>alert('Hanya file berformat .PDF yang diperbolehkan!');</script>";
    } else {
        // Cek apakah kode SOP sudah ada
        $check = mysqli_query($conn, "SELECT id_sop FROM tb_sop WHERE kode_sop = '$kode'");
        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Kode SOP [$kode] sudah terdaftar. Silakan gunakan kode lain.');</script>";
        } else {
            $original_name = basename($_FILES['file']['name']);
            $clean_name    = preg_replace('/[^A-Za-z0-9_\-\. ]/', '_', $original_name);
            
            // Hindari replace file jika ada nama sama
            if (file_exists($targetdir . $clean_name)) {
                $clean_name = time() . '_' . $clean_name;
            }

            $targetpath = $targetdir . $clean_name;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetpath)) {
                $sql_insert = "INSERT INTO tb_sop (nama_sop, kode_sop, tipe_sop, namafile_sop, is_karisma, is_prioritas)
                               VALUES ('$nama', '$kode', '$tipe', '$clean_name', $iska, $ispri)";
                if (mysqli_query($conn, $sql_insert)) {
                    echo "<script>alert('SOP berhasil diupload!'); window.location.href='updatesop';</script>";
                    exit();
                } else {
                    echo "<script>alert('Gagal menyimpan ke database: " . mysqli_error($conn) . "');</script>";
                }
            } else {
                echo "<script>alert('Gagal mengunggah file ke direktori server.');</script>";
            }
        }
    }
}

// Data SOP & Statistik
$resultsop     = mysqli_query($conn, "SELECT * FROM tb_sop ORDER BY id_sop DESC");
$total_sop     = mysqli_num_rows($resultsop);

$q_karisma     = mysqli_query($conn, "SELECT COUNT(*) as jml FROM tb_sop WHERE is_karisma = 1");
$jml_karisma   = mysqli_fetch_assoc($q_karisma)['jml'] ?? 0;

$q_prioritas   = mysqli_query($conn, "SELECT COUNT(*) as jml FROM tb_sop WHERE is_prioritas = 1");
$jml_prioritas = mysqli_fetch_assoc($q_prioritas)['jml'] ?? 0;

$q_dept        = mysqli_query($conn, "SELECT COUNT(DISTINCT tipe_sop) as jml FROM tb_sop");
$jml_dept      = mysqli_fetch_assoc($q_dept)['jml'] ?? 0;

$user_dept     = !empty($departement) ? $departement : (!empty($bagian) ? $bagian : 'Umum');
?>
<!DOCTYPE html>
<html lang="en">
<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Top Navbar -->
        <nav class="app-header navbar navbar-expand bg-body shadow-sm">
            <div class="container-fluid">
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item d-none d-md-block">
                        <a href="sop" class="nav-link">SOP Karisma</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="sop-prioritas" class="nav-link">SOP Prioritas</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="sop-departemen" class="nav-link">SOP Departemen</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="updatesop" class="nav-link active text-primary fw-bold">
                            <i class="bi bi-gear-fill me-1"></i>Kelola / Upload SOP
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img style="margin-top: -2px;" src="assets/img/profile.png" class="user-image rounded-circle shadow-sm" alt="User Image">
                            <span class="d-none d-md-inline"><?php echo $username; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li class="user-header bg-primary text-white text-center p-3">
                                <p class="mb-0 fw-bold"><?= $nama_lngkp ?: $username ?></p>
                                <small><?= $jabatan ?> - <?= $departement ?></small>
                            </li>
                            <li class="user-footer d-flex justify-content-between p-2">
                                <a href="sop" class="btn btn-outline-secondary btn-sm">Katalog SOP</a>
                                <a href="logout" class="btn btn-danger btn-sm">Sign Out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar Navigation -->
        <?php include("pages/part/p_aside.php"); ?>

        <!-- Main Content Wrapper -->
        <main class="app-main">
            <div class="app-content-header py-3">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-folder2-open text-primary me-2"></i>Kelola SOP Digital
                            </h3>
                            <p class="text-muted mb-0 small">Manajemen dokumen SOP, unggah file PDF baru, dan pratinjau dokumen.</p>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <button type="button" class="btn btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUploadSOP">
                                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload SOP Baru
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content pb-5">
                <div class="container-fluid">
                    <!-- Stat Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm h-100 rounded-3">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold">Total Dokumen</div>
                                    <div class="fs-4 fw-bold text-dark mt-1"><?= number_format($total_sop) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm h-100 rounded-3">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold">SOP Karisma</div>
                                    <div class="fs-4 fw-bold text-dark mt-1"><?= number_format($jml_karisma) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm h-100 rounded-3">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold">SOP Prioritas</div>
                                    <div class="fs-4 fw-bold text-dark mt-1"><?= number_format($jml_prioritas) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm h-100 rounded-3">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold">Departemen</div>
                                    <div class="fs-4 fw-bold text-dark mt-1"><?= number_format($jml_dept) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Table Card -->
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-table me-2 text-secondary"></i>Daftar Master SOP
                            </h5>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                <?= $total_sop ?> SOP Tersedia
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive">
                                <table id="tableSOP" class="table table-hover table-striped align-middle w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 50px;">No</th>
                                            <th style="width: 140px;">Kode SOP</th>
                                            <th>Nama SOP</th>
                                            <th style="width: 150px;">Departemen</th>
                                            <th style="width: 180px;">Kategori SOP</th>
                                            <th style="width: 180px;">File Dokumen</th>
                                            <th class="text-center" style="width: 120px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        mysqli_data_seek($resultsop, 0);
                                        while ($row = mysqli_fetch_assoc($resultsop)) { 
                                            $file_sop_path = "assets/pdf/sop/" . $row['namafile_sop'];
                                            $file_exists   = file_exists($file_sop_path);
                                        ?>
                                            <tr>
                                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                                <td>
                                                    <span class="badge bg-dark-subtle text-dark border px-2 py-1 font-monospace">
                                                        <?= htmlspecialchars($row['kode_sop']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_sop']) ?></div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border px-2 py-1">
                                                        <?= htmlspecialchars($row['tipe_sop'] ?: '-') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <?php if ($row['is_karisma'] == 1): ?>
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Karisma</span>
                                                        <?php endif; ?>
                                                        <?php if ($row['is_prioritas'] == 1): ?>
                                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Prioritas</span>
                                                        <?php endif; ?>
                                                        <?php if ($row['is_karisma'] != 1 && $row['is_prioritas'] != 1): ?>
                                                            <span class="badge bg-secondary-subtle text-muted">Standar</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($file_exists): ?>
                                                        <span class="text-truncate d-inline-block small text-muted" style="max-width: 170px;" title="<?= htmlspecialchars($row['namafile_sop']) ?>">
                                                            <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i><?= htmlspecialchars($row['namafile_sop']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger-subtle text-danger" title="File fisik tidak ditemukan di folder assets/pdf/sop/">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>File Hilang
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <?php if ($file_exists): ?>
                                                            <button type="button" class="btn btn-outline-primary"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#previewSopModal"
                                                                    data-sop-url="<?= htmlspecialchars($file_sop_path) ?>"
                                                                    data-sop-title="<?= htmlspecialchars($row['kode_sop'] . ' - ' . $row['nama_sop']) ?>"
                                                                    title="Preview PDF">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-outline-secondary disabled" title="File tidak ditemukan">
                                                                <i class="bi bi-eye-slash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <a href="updatesop?delete_sop=<?= $row['id_sop'] ?>" 
                                                           class="btn btn-outline-danger"
                                                           onclick="return confirm('Apakah Anda yakin ingin menghapus SOP [<?= addslashes($row['kode_sop']) ?>] ini?')"
                                                           title="Hapus SOP">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Reusable Modal Preview PDF -->
        <div class="modal fade" id="previewSopModal" tabindex="-1" aria-labelledby="previewModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-light py-2">
                        <h5 class="modal-title fw-bold text-dark text-truncate" id="previewModalTitle" style="max-width: 80%;">
                            Preview Dokumen SOP
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <a id="previewModalNewTab" href="#" target="_blank" class="btn btn-sm btn-outline-secondary" title="Buka di tab baru">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Tab Baru
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body p-0" style="background-color: #525659;">
                        <iframe id="previewModalFrame" src="" width="100%" height="700px" style="border: none;"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Upload SOP Baru -->
        <div class="modal fade" id="modalUploadSOP" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalUploadLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow border-0">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="modalUploadLabel">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i>Upload Dokumen SOP Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="kodesop" class="form-label fw-semibold">Kode SOP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="kodesop" id="kodesop" placeholder="Contoh: Sales 282, HRD 015" required>
                                <div class="form-text">Pastikan kode SOP unik dan belum pernah digunakan sebelumnya.</div>
                            </div>
                            <div class="mb-3">
                                <label for="namasop" class="form-label fw-semibold">Nama SOP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="namasop" id="namasop" placeholder="Contoh: SOP Membuat Sales Program" required>
                            </div>
                            <div class="mb-3">
                                <div class="p-2 px-3 bg-light rounded-2 border d-flex justify-content-between align-items-center">
                                    <span class="small text-muted"><i class="bi bi-building me-1"></i>Departemen:</span>
                                    <span class="badge bg-primary-subtle text-primary border px-2 py-1 font-monospace"><?= htmlspecialchars($user_dept) ?></span>
                                </div>
                                <div class="form-text">Otomatis disesuaikan dengan departemen akun Anda.</div>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label fw-semibold">File Dokumen SOP (.PDF) <span class="text-danger">*</span></label>
                                <input class="form-control" type="file" name="file" accept=".pdf" id="file" required>
                                <div class="form-text">Hanya menerima format dokumen PDF.</div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold d-block">Kategori / Opsi SOP</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="inlineCheckbox1" id="inlineCheckbox1" value="1">
                                    <label class="form-check-label" for="inlineCheckbox1">SOP Karisma</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="inlineCheckbox2" id="inlineCheckbox2" value="1">
                                    <label class="form-check-label" for="inlineCheckbox2">SOP Prioritas</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="submitsop" class="btn btn-primary px-4">
                                <i class="bi bi-upload me-1"></i> Unggah SOP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include("pages/part/p_footer.php"); ?>
    </div>

    <!-- DataTables & Dynamic Preview Script -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tableSOP').DataTable({
                responsive: true,
                language: {
                    search: "Cari SOP:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ SOP",
                    infoEmpty: "Tidak ada data SOP",
                    zeroRecords: "Data SOP tidak ditemukan",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 10,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [6] }
                ]
            });

            // Reusable modal preview handler
            const previewModal = document.getElementById('previewSopModal');
            if (previewModal) {
                previewModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const fileUrl = button.getAttribute('data-sop-url');
                    const title = button.getAttribute('data-sop-title');
                    
                    document.getElementById('previewModalTitle').textContent = title;
                    document.getElementById('previewModalFrame').src = fileUrl;
                    document.getElementById('previewModalNewTab').href = fileUrl;
                });
                
                previewModal.addEventListener('hidden.bs.modal', function() {
                    document.getElementById('previewModalFrame').src = '';
                });
            }
        });
    </script>
</body>
</html>