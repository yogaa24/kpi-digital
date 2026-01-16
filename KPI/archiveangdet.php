<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/configarchive.php';
    require 'helper/getUser.php';

// Handler untuk edit what (WHAT A dan WHAT B)
if (isset($_POST['what_edit'])) {
    $ids = $_GET['id'];
    $idar = $_GET['idar'];
    $idw = intval($_POST['idkw']);
    $tujuan = mysqli_real_escape_string($connarc, $_POST['tujuanw']);
    $bobot = floatval($_POST['bobotw']);
    $editor_id = $_SESSION['id_user']; // ID atasan yang mengedit
    
    // Ambil tipe what
    $sql_check = "SELECT tipe_what FROM tbar_whats WHERE id_what = $idw";
    $result_check = mysqli_query($connarc, $sql_check);
    $data_check = mysqli_fetch_assoc($result_check);
    $tipe_what = $data_check['tipe_what'];
    
    // Update tbar_whats dengan penanda edited
    $sql = "UPDATE tbar_whats 
            SET p_what='$tujuan', 
                bobot=$bobot, 
                is_edited=1, 
                edited_by=$editor_id, 
                edited_at=NOW() 
            WHERE id_what=$idw AND id_user='$ids'";
    
    if (mysqli_query($connarc, $sql)) {
        // Jika What A, kelola indikator
        if ($tipe_what == 'A') {
            // Hapus indikator yang ditandai untuk dihapus
            if (isset($_POST['indikator_hapus']) && is_array($_POST['indikator_hapus'])) {
                foreach ($_POST['indikator_hapus'] as $id_hapus) {
                    $id_hapus = intval($id_hapus);
                    mysqli_query($connarc, "DELETE FROM tbar_indikator_whats WHERE id_indikator = $id_hapus");
                }
            }
            
            // Update atau insert indikator
            if (isset($_POST['indikator_keterangan']) && isset($_POST['indikator_nilai']) && isset($_POST['indikator_id'])) {
                $keterangans = $_POST['indikator_keterangan'];
                $nilais = $_POST['indikator_nilai'];
                $ids_indikator = $_POST['indikator_id'];
                
                $count = min(count($keterangans), count($nilais), count($ids_indikator));
                
                for ($i = 0; $i < $count; $i++) {
                    if (!empty(trim($keterangans[$i])) && $nilais[$i] !== '') {
                        $ket = mysqli_real_escape_string($connarc, trim($keterangans[$i]));
                        $nil = floatval($nilais[$i]);
                        $id_indi = intval($ids_indikator[$i]);
                        $urutan = $i + 1;
                        
                        if ($id_indi > 0) {
                            // Update indikator yang sudah ada - TAMBAH PENANDA EDIT
                            $sql_update = "UPDATE tbar_indikator_whats 
                                           SET keterangan='$ket', 
                                               nilai=$nil, 
                                               urutan=$urutan,
                                               is_edited=1,
                                               edited_by=$editor_id,
                                               edited_at=NOW()
                                           WHERE id_indikator=$id_indi";
                            mysqli_query($connarc, $sql_update);
                        } else {
                            // Insert indikator baru - TANDAI SEBAGAI BARU (EDITED)
                            $sql_insert = "INSERT INTO tbar_indikator_whats 
                                          (id_what, keterangan, nilai, urutan, is_edited, edited_by, edited_at) 
                                           VALUES ($idw, '$ket', $nil, $urutan, 1, $editor_id, NOW())";
                            mysqli_query($connarc, $sql_insert);
                        }
                    }
                }
            }
        }
        
        header('Location: archiveangdet.php?id=' . $ids . '&idar=' . urlencode($idar));
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate What')</script>";
    }
}

// Handler untuk penilaian what (WHAT A dan WHAT B)
if (isset($_POST['nilai_what'])) {
    $ids = $_GET['id'];
    $idar = $_GET['idar'];
    $id_what = intval($_POST['idkpi']);
    $editor_id = $_SESSION['id_user'];
    
    // Ambil data what untuk cek tipe
    $sql_what = "SELECT tipe_what, bobot FROM tbar_whats WHERE id_what = $id_what";
    $result_what = mysqli_query($connarc, $sql_what);
    $data_what = mysqli_fetch_assoc($result_what);
    $tipe_what = $data_what['tipe_what'];
    $bobot = $data_what['bobot'];
    
    if ($tipe_what == 'A') {
        // WHAT A: Ambil nilai dari indikator yang dipilih
        $id_indikator = intval($_POST['nilaisi']);
        
        $sql_get = "SELECT nilai, keterangan FROM tbar_indikator_whats WHERE id_indikator = $id_indikator";
        $result_get = mysqli_query($connarc, $sql_get);
        $data = mysqli_fetch_assoc($result_get);
        $nilai = $data['nilai'];
        $keterangan = mysqli_real_escape_string($connarc, $data['keterangan']);
        
        // Hitung total
        $total = number_format($nilai * $bobot / 100, 2);
        
        // Update tbar_whats - TAMBAH PENANDA EDIT
        $sql_update = "UPDATE tbar_whats 
                       SET nilai = $nilai, 
                           hasil = '$keterangan', 
                           total = $total,
                           is_edited=1,
                           edited_by=$editor_id,
                           edited_at=NOW()
                       WHERE id_what = $id_what AND id_user = '$ids'";
        
    } else {
        // WHAT B: Hitung dari target omset dan hasil
        $target_omset = floatval($_POST['target_omset']);
        $hasil_omset = floatval($_POST['hasil_omset']);
        
        if ($target_omset > 0) {
            $persentase = ($hasil_omset / $target_omset) * 100;
            $nilai = round($persentase, 2);
        } else {
            $nilai = 0;
        }
        
        $total = number_format($nilai * $bobot / 100, 2);
        $hasil_text = " Hasil Tercapai: " . number_format($hasil_omset, 2);
        $hasil_text = mysqli_real_escape_string($connarc, $hasil_text);
        
        // Update tbar_whats - TAMBAH PENANDA EDIT
        $sql_update = "UPDATE tbar_whats 
                       SET target_omset = $target_omset, 
                           nilai = $nilai, 
                           hasil = '$hasil_text', 
                           total = $total,
                           is_edited=1,
                           edited_by=$editor_id,
                           edited_at=NOW()
                       WHERE id_what = $id_what AND id_user = '$ids'";
    }
    
    if (mysqli_query($connarc, $sql_update)) {
        header('Location: archiveangdet.php?id=' . $ids . '&idar=' . urlencode($idar));
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan penilaian: " . mysqli_error($connarc) . "')</script>";
    }
}

// Handler untuk hapus what
if (isset($_POST['what_hapus'])) {
    $ids = $_GET['id'];
    $idar = $_GET['idar'];
    $idkpi = intval($_POST['idkwd']);

    // Hapus indikator terkait terlebih dahulu
    mysqli_query($connarc, "DELETE FROM tbar_indikator_whats WHERE id_what = $idkpi");
    
    // Hapus what
    $sql = "DELETE FROM tbar_whats WHERE id_what=$idkpi AND id_user=$ids";
    $result = mysqli_query($connarc, $sql);
    
    if ($result) {
        header('Location: archiveangdet.php?id=' . $ids . '&idar=' . urlencode($idar));
        exit();
    } else {
        echo "<script>alert('Gagal menghapus What')</script>";
    }
}

// Handler untuk edit how (HOW A dan HOW B)
if (isset($_POST['how_edit'])) {
    $ids = $_GET['id'];
    $idar = $_GET['idar'];
    $idh = intval($_POST['idkh']);
    $tujuan = mysqli_real_escape_string($connarc, $_POST['tujuanh']);
    $bobot = floatval($_POST['boboth']);
    $editor_id = $_SESSION['id_user'];
    
    // Ambil tipe how
    $sql_check = "SELECT tipe_how FROM tbar_hows WHERE id_how = $idh";
    $result_check = mysqli_query($connarc, $sql_check);
    $data_check = mysqli_fetch_assoc($result_check);
    $tipe_how = $data_check['tipe_how'];
    
    // Update tbar_hows - TAMBAH PENANDA EDIT
    $sql = "UPDATE tbar_hows 
            SET p_how='$tujuan', 
                bobot=$bobot,
                is_edited=1,
                edited_by=$editor_id,
                edited_at=NOW()
            WHERE id_how=$idh AND id_user='$ids'";
    
    if (mysqli_query($connarc, $sql)) {
        // Jika How A, kelola indikator
        if ($tipe_how == 'A') {
            if (isset($_POST['indikator_hapus']) && is_array($_POST['indikator_hapus'])) {
                foreach ($_POST['indikator_hapus'] as $id_hapus) {
                    $id_hapus = intval($id_hapus);
                    mysqli_query($connarc, "DELETE FROM tbar_indikator_hows WHERE id_indikator = $id_hapus");
                }
            }
            
            if (isset($_POST['indikator_keterangan']) && isset($_POST['indikator_nilai'])) {
                $keterangans = $_POST['indikator_keterangan'];
                $nilais = $_POST['indikator_nilai'];
                $ids_indikator = $_POST['indikator_id'];
                
                for ($i = 0; $i < count($keterangans); $i++) {
                    if (!empty($keterangans[$i])) {
                        $ket = mysqli_real_escape_string($connarc, $keterangans[$i]);
                        $nil = floatval($nilais[$i]);
                        $id_indi = intval($ids_indikator[$i]);
                        $urutan = $i + 1;
                        
                        if ($id_indi > 0) {
                            // Update - TAMBAH PENANDA
                            $sql_update = "UPDATE tbar_indikator_hows 
                                           SET keterangan='$ket', 
                                               nilai=$nil, 
                                               urutan=$urutan,
                                               is_edited=1,
                                               edited_by=$editor_id,
                                               edited_at=NOW()
                                           WHERE id_indikator=$id_indi";
                            mysqli_query($connarc, $sql_update);
                        } else {
                            // Insert baru - TANDAI EDITED
                            $sql_insert = "INSERT INTO tbar_indikator_hows 
                                          (id_how, keterangan, nilai, urutan, is_edited, edited_by, edited_at) 
                                           VALUES ($idh, '$ket', $nil, $urutan, 1, $editor_id, NOW())";
                            mysqli_query($connarc, $sql_insert);
                        }
                    }
                }
            }
        }
        
        header('Location: archiveangdet.php?id=' . $ids . '&idar=' . urlencode($idar));
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate How')</script>";
    }
}

// Handler untuk penilaian how (HOW A dan HOW B)
if (isset($_POST['nilai_how'])) {
    $ids = $_GET['id'];
    $idar = $_GET['idar'];
    $id_how = intval($_POST['idkpi']);
    $editor_id = $_SESSION['id_user'];
    
    // Ambil data how untuk cek tipe
    $sql_how = "SELECT tipe_how, bobot FROM tbar_hows WHERE id_how = $id_how";
    $result_how = mysqli_query($connarc, $sql_how);
    $data_how = mysqli_fetch_assoc($result_how);
    $tipe_how = $data_how['tipe_how'];
    $bobot = $data_how['bobot'];
    
    if ($tipe_how == 'A') {
        $id_indikator = intval($_POST['nilaisi']);
        
        $sql_get = "SELECT nilai, keterangan FROM tbar_indikator_hows WHERE id_indikator = $id_indikator";
        $result_get = mysqli_query($connarc, $sql_get);
        $data = mysqli_fetch_assoc($result_get);
        $nilai = $data['nilai'];
        $keterangan = mysqli_real_escape_string($connarc, $data['keterangan']);
        
        $total = number_format($nilai * $bobot / 100, 2);
        
        // Update - TAMBAH PENANDA
        $sql_update = "UPDATE tbar_hows 
                       SET nilai = $nilai, 
                           hasil = '$keterangan', 
                           total = $total,
                           is_edited=1,
                           edited_by=$editor_id,
                           edited_at=NOW()
                       WHERE id_how = $id_how AND id_user = '$ids'";
        
    } else {
        $target_omset = floatval($_POST['target_omset']);
        $hasil_omset = floatval($_POST['hasil_omset']);
        
        if ($target_omset > 0) {
            $persentase = ($hasil_omset / $target_omset) * 100;
            $nilai = $persentase;
        } else {
            $nilai = 0;
        }
        
        $total = number_format($nilai * $bobot / 100, 2);
        $hasil_text = " Hasil Tercapai: " . number_format($hasil_omset, 2);
        $hasil_text = mysqli_real_escape_string($connarc, $hasil_text);
        
        // Update - TAMBAH PENANDA
        $sql_update = "UPDATE tbar_hows 
                       SET target_omset = $target_omset, 
                           nilai = $nilai, 
                           hasil = '$hasil_text', 
                           total = $total,
                           is_edited=1,
                           edited_by=$editor_id,
                           edited_at=NOW()
                       WHERE id_how = $id_how AND id_user = '$ids'";
    }
    
    if (mysqli_query($connarc, $sql_update)) {
        header('Location: archiveangdet.php?id=' . $ids . '&idar=' . urlencode($idar));
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan penilaian: " . mysqli_error($connarc) . "')</script>";
    }
}

// Handler untuk hapus how
if (isset($_POST['how_hapus'])) {
    $ids = $_GET['id'];
    $idar = $_GET['idar'];
    $idkpi = intval($_POST['idkhd']);

    // Hapus indikator terkait terlebih dahulu
    mysqli_query($connarc, "DELETE FROM tbar_indikator_hows WHERE id_how = $idkpi");
    
    // Hapus how
    $sql = "DELETE FROM tbar_hows WHERE id_how=$idkpi AND id_user=$ids";
    $result = mysqli_query($connarc, $sql);
    
    if ($result) {
        header('Location: archiveangdet.php?id=' . $ids . '&idar=' . urlencode($idar));
        exit();
    } else {
        echo "<script>alert('Gagal menghapus How')</script>";
    }
}

    $id_user = $_GET['id'];
$idar = $_GET['idar'];

$sql= "SELECT tbar_kpi.* FROM tbar_kpi INNER JOIN tbar_archive ON tbar_archive.id_archive = tbar_kpi.id_arcv WHERE tbar_archive.bulan = '$idar' AND tbar_archive.id_user = $id_user";
$result = mysqli_query($connarc, $sql);
$idKPI;
$idUSER;
$poin;
$bobot;
$poin2;
$bobot2;

$sql2 = "SELECT sum(bobot) FROM tbar_kpi WHERE id_user=$id_user AND bulan = '$idar'";
$result2 = mysqli_query($connarc, $sql2);

}
?>

<html lang="en">

<?php include("pages/part/p_header.php"); ?>
<style>
.edited-badge {
    background-color: #ffc107;
    color: #000;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 3px;
    margin-left: 5px;
    font-weight: bold;
}

.edited-row {
    background-color: #fff3cd !important;
}

.edited-info {
    font-size: 11px;
    color: #856404;
    font-style: italic;
}
</style>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="archiveangpoin?id=<?= $id_user ?>&idar=<?= urlencode($idar) ?>" class="nav-link">
                            Kembali
                        </a>
                    </li>
                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a> </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"> <img style="margin-top: -2px;" src="assets/img/profile.png" class="user-image rounded-circle shadow" alt="User Image"> <span class="d-none d-md-inline"><?php echo $username?></span> </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end">
                            <li class="user-footer">
                                <center> <a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a></center>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="dashboard" class="brand-link">
                    <img src="assets/img/logokpi.png" alt="Logo" class="brand-image opacity-100">
                    <span class="brand-text fw-light">KPI Digital</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                        <li class="nav-item"> <a href="dashboard" class="nav-link"> <i class="nav-icon bi bi-plus-circle"></i>
                                <p>KPI & SS</p>
                            </a>
                        </li>

                        <li class="nav-item"> <a href="archive" class="nav-link"> <i class="nav-icon bi bi-archive"></i>
                                <p>Archive</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item"> <a href="sop" class="nav-link"> <i class="nav-icon bi bi-journal-bookmark"></i>
                                <p>SOP</p>
                            </a>
                        </li> -->
                        <li class="nav-item"> <a href="eviden" class="nav-link"> <i class="nav-icon bi bi-box2"></i>
                                <p>Eviden</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="mt-3">
                <!--begin::Container-->
                <!-- isi -->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid" style="font-size:13px;">
                    <!--begin::Row-->
                    <?php while ($hasil = mysqli_fetch_assoc($result)) {
                        $idKPI = $hasil['id'];
                        $poin = $hasil['poin'];
                        $bobot = $hasil['bobot'];
                        $poin2 = $hasil['poin2'];
                        $bobot2 = $hasil['bobot2'];
                    ?>
                    <div class="row">
                        <div class="col-lg connectedSortable">
                            <div class="d-flex">
                                <!-- CARD WHAT -->
                                <div class="card mb-4 w-50" style="margin-right:7px;">
                                    <div style="height: 50px; margin-top: -3px;" class="card-header bg-primary">
                                        <h5 style="color:white;" class="card-title"><?= $poin; ?></h5>
                                        <h5 style="color:white; margin-left: 15px;" class="badge text-bg-warning fs-7 fw-bolder">Bobot :
                                            <?= $bobot ?>%
                                        </h5>
                                        <div class="card-tools">
                                            <button style="color: white;" type="button" class="btn btn-tool"
                                                data-lte-toggle="card-collapse">
                                                <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                                                <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Whats</th>
                                                    <th style="width: 30%">Hasil</th>
                                                    <th style="width: 5%">
                                                        <center>Nilai</center>
                                                    </th>
                                                    <th style="width: 4%">
                                                        <center>Bobot</center>
                                                    </th>
                                                    <th style="width: 4%">
                                                        <center>Total</center>
                                                    </th>
                                                    <th style="width: 9%">
                                                        <center>Action</center>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                            $sql1 = "SELECT * FROM tbar_whats WHERE id_user='$id_user' AND id_kpi='" . $hasil['id'] . "'";
                                            $ql = mysqli_query($connarc, $sql1);
                                            while ($res = mysqli_fetch_assoc($ql)) {
                                            ?>
                                                <tr class="align-middle <?= $res['is_edited'] == 1 ? 'edited-row' : '' ?>">
                                                    <td>
                                                        <?= $res['p_what']; ?>
                                                        <?php if ($res['is_edited'] == 1) { ?>
                                                            <span class="edited-badge">DIUBAH</span>
                                                            <br><small class="edited-info">
                                                                Diubah: <?= date('d/m/Y H:i', strtotime($res['edited_at'])) ?>
                                                            </small>
                                                        <?php } ?>
                                                        <?php if ($res['tipe_what'] == 'B' && $res['target_omset'] > 0) { ?>
                                                            <br><small class="text-muted fw-semibold fs-6">Target: <?=number_format($res['target_omset'], 2)?></small>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?= $res['hasil']; ?></td>
                                                    <td>
                                                        <center><?= $res['nilai']; ?></center>
                                                    </td>
                                                    <td>
                                                        <center><?= $res['bobot']; ?>%</center>
                                                    </td>
                                                    <td>
                                                        <center><?= $res['total']; ?></center>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" data-bs-toggle="dropdown" class="btn btn-success btn-sm">
                                                            <i class="bi bi-eye fs-8"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                            <a value="<?php echo $res['id_what']; ?>" name="what_edit" class="dropdown-item"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#EditWhatModal<?= $res['id_what'] ?>">Edit</a> 
                                                            <a class="dropdown-item" data-bs-toggle="modal"
                                                                data-bs-target="#HapusWhatModal<?= $res['id_what'] ?>">Hapus</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item fw-bolder" data-bs-toggle="modal"
                                                                data-bs-target="#NilaiWhatModal<?= $res['id_what'] ?>">Nilai</a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <?php
                                                // Ambil data what
                                                $tipe_what = $res['tipe_what'];
                                                $target_omset = $res['target_omset'];
                                                ?>

                                                <!-- Modal Nilai What -->
                                                <div class="modal fade" id="NilaiWhatModal<?=$res['id_what']?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content"> 
                                                        
                                                            <!-- Tambahkan style ini -->
                                                            <style>
                                                                #NilaiWhatModal<?=$res['id_what']?> select option {
                                                                    white-space: normal;
                                                                    word-wrap: break-word;
                                                                    overflow-wrap: break-word;
                                                                    max-width: 100%;
                                                                }
                                                                
                                                                #NilaiWhatModal<?=$res['id_what']?> select {
                                                                    max-width: 100%;
                                                                }
                                                            </style>
                                                            
                                                            <div class="modal-header"> 
                                                                <h5 class="modal-title fw-bold" id="exampleModalLabel">
                                                                    Penilaian What <?=$tipe_what?>
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action="">
                                                                    <input type="hidden" value="<?php echo $res['id_what']; ?>" name="idkpi"> 
                                                                    
                                                                    <div class="input-group mb-3">
                                                                        <span style="color: #343A40;" class="input-group-text fw-bold" id="tujuan">Tujuan :</span>
                                                                        <textarea type="input" class="form-control" name="indikatorwhat" disabled placeholder="" aria-label="Tujuan KPI" aria-describedby="tujuan"><?=$res['p_what']?></textarea>
                                                                    </div>
                                                                    
                                                                    <?php if ($tipe_what == 'A') { ?>
                                                                        <!-- WHAT A: Pilih dari indikator -->
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">Pilih Nilai Penilaian:</label>
                                                                            <select required class="form-select" name="nilaisi" id="nilaisi<?=$res['id_what']?>">
                                                                                <option selected disabled>-- Pilih Nilai --</option>
                                                                                <?php 
                                                                                $id_what = $res['id_what'];
                                                                                $sql_indikator = "SELECT * FROM tbar_indikator_whats 
                                                                                                WHERE id_what = '$id_what' 
                                                                                                ORDER BY urutan ASC";
                                                                                $result_indikator = mysqli_query($connarc, $sql_indikator);
                                                                                
                                                                                while ($indikator = mysqli_fetch_assoc($result_indikator)) {
                                                                                    // Potong keterangan jika terlalu panjang untuk ditampilkan
                                                                                    $ket_display = strlen($indikator['keterangan']) > 80 
                                                                                                ? substr($indikator['keterangan'], 0, 80) . '...' 
                                                                                                : $indikator['keterangan'];
                                                                                    
                                                                                    echo '<option value="'.$indikator['id_indikator'].'" title="'.$indikator['keterangan'].'">';
                                                                                    echo htmlspecialchars($ket_display) . ' = ' . $indikator['nilai'];
                                                                                    echo '</option>';
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                            <small class="text-muted">Hover pada pilihan untuk melihat keterangan lengkap</small>
                                                                        </div>
                                                                    <?php } else { ?>
                                                                        <!-- WHAT B: Input target omset dan hasil -->
                                                                        <div class="input-group mb-3">
                                                                            <span style="color: #343A40;" class="input-group-text fw-bold">Target Omset :</span>
                                                                            <input type="number" step="0.01" class="form-control" name="target_omset" 
                                                                                value="<?=$target_omset?>" required placeholder="Contoh: 1000000">
                                                                        </div>
                                                                        
                                                                        <div class="input-group mb-3">
                                                                            <span style="color: #343A40;" class="input-group-text fw-bold">Hasil Omset :</span>
                                                                            <input type="number" step="0.01" class="form-control" name="hasil_omset" 
                                                                                required placeholder="Hasil yang dicapai">
                                                                        </div>
                                                                    <?php } ?>
                                                                    
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" name="nilai_what" class="btn btn-primary">Simpan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php include('pages/kpi/k_modalEditwhat.php'); ?>
                                                <?php include('pages/kpi/k_modalHapuswhat.php'); ?>

                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div> <!-- /.card-body -->
                                </div> <!-- /.card -->
                                
                                <!-- CARD HOW -->
                                <div class="card mb-4 w-50" style="margin-left:7px;">
                                    <div style="height: 50px; margin-top: -3px;" class="card-header bg-success">
                                        <h5 style="color:white;" class="card-title"><?= $poin2; ?></h5>
                                        <h5 style="color:white; margin-left: 15px;" class="badge text-bg-warning fs-7 fw-bolder">
                                            Bobot :
                                            <?= $bobot2 ?>%
                                        </h5>
                                        <div class="card-tools">
                                            <button style="color: white;" type="button" class="btn btn-tool"
                                                data-lte-toggle="card-collapse">
                                                <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                                                <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Hows</th>
                                                    <th style="width: 30%">Hasil</th>
                                                    <th style="width: 5%">
                                                        <center>Nilai</center>
                                                    </th>
                                                    <th style="width: 4%">
                                                        <center>Bobot</center>
                                                    </th>
                                                    <th style="width: 4%">
                                                        <center>Total</center>
                                                    </th>
                                                    <th style="width: 9%">
                                                        <center>Action</center>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $sql1 = "SELECT * FROM tbar_hows WHERE id_user='$id_user' AND id_kpi='" . $hasil['id'] . "'";
                                            $ql = mysqli_query($connarc, $sql1);
                                            while ($res = mysqli_fetch_assoc($ql)) {
                                            ?>
                                                <tr class="align-middle <?= $res['is_edited'] == 1 ? 'edited-row' : '' ?>">
                                                    <td>
                                                        <?= $res['p_how']; ?>
                                                        <?php if ($res['is_edited'] == 1) { ?>
                                                            <span class="edited-badge">DIUBAH</span>
                                                            <br><small class="edited-info">
                                                                Diubah: <?= date('d/m/Y H:i', strtotime($res['edited_at'])) ?>
                                                            </small>
                                                        <?php } ?>
                                                        <?php if ($res['tipe_how'] == 'B' && $res['target_omset'] > 0) { ?>
                                                            <br><small class="text-muted fw-semibold fs-6">Target: <?=number_format($res['target_omset'], 2)?></small>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?= $res['hasil']; ?></td>
                                                    <td>
                                                        <center><?= $res['nilai']; ?></center>
                                                    </td>
                                                    <td>
                                                        <center><?= $res['bobot']; ?>%</center>
                                                    </td>
                                                    <td>
                                                        <center><?= $res['total']; ?></center>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" data-bs-toggle="dropdown" class="btn btn-success btn-sm">
                                                            <i class="bi bi-eye fs-8"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                            <a value="<?php echo $res['id_how']; ?>" name="how_edit" class="dropdown-item"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#EditHowModal<?= $res['id_how'] ?>">Edit</a>
                                                            <a class="dropdown-item" data-bs-toggle="modal"
                                                                data-bs-target="#HapusHowModal<?= $res['id_how'] ?>">Hapus</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item fw-bolder" data-bs-toggle="modal"
                                                                data-bs-target="#NilaiHowModal<?= $res['id_how'] ?>">Nilai</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                
                                                <?php
                                                $tipe_how = $res['tipe_how'];
                                                $target_omset = $res['target_omset'];
                                                ?>

                                                <!-- Modal Nilai How -->
                                                <div class="modal fade" id="NilaiHowModal<?=$res['id_how']?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content"> 
                                                            <div class="modal-header"> 
                                                                <h5 class="modal-title fw-bold" id="exampleModalLabel">
                                                                    Penilaian How <?=$tipe_how?>
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action="">
                                                                    <input type="hidden" value="<?php echo $res['id_how']; ?>" name="idkpi"> 
                                                                    
                                                                    <div class="input-group mb-3">
                                                                        <span style="color: #343A40;" class="input-group-text fw-bold" id="tujuan">Tujuan :</span>
                                                                        <textarea type="input" class="form-control" name="indikatorhow" disabled placeholder="" aria-label="Tujuan KPI" aria-describedby="tujuan"><?=$res['p_how']?></textarea>
                                                                    </div>
                                                                    
                                                                    <?php if ($tipe_how == 'A') { ?>
                                                                        <!-- HOW A: Pilih dari indikator -->
                                                                        <div class="input-group mb-3">
                                                                            <span style="color: #343A40;" class="input-group-text fw-bold">Nilai :</span>
                                                                            <select required class="form-control" name="nilaisi" id="nilaisi">
                                                                                <option selected disabled>Pilih Nilai</option>
                                                                                <?php 
                                                                                $id_how = $res['id_how'];
                                                                                $sql_indikator = "SELECT * FROM tbar_indikator_hows 
                                                                                                WHERE id_how = '$id_how' 
                                                                                                ORDER BY urutan ASC";
                                                                                $result_indikator = mysqli_query($connarc, $sql_indikator);
                                                                                
                                                                                while ($indikator = mysqli_fetch_assoc($result_indikator)) {
                                                                                    echo '<option value="'.$indikator['id_indikator'].'">';
                                                                                    echo ''.$indikator['keterangan'].' = '.$indikator['nilai'];
                                                                                    echo '</option>';
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    <?php } else { ?>
                                                                        <!-- HOW B: Input target omset dan hasil -->
                                                                        <div class="input-group mb-3">
                                                                            <span style="color: #343A40;" class="input-group-text fw-bold">Target Omset :</span>
                                                                            <input type="number" step="0.01" class="form-control" name="target_omset" 
                                                                                value="<?=$target_omset?>" required placeholder="Contoh: 1000000">
                                                                        </div>
                                                                        
                                                                        <div class="input-group mb-3">
                                                                            <span style="color: #343A40;" class="input-group-text fw-bold">Hasil Omset :</span>
                                                                            <input type="number" step="0.01" class="form-control" name="hasil_omset" 
                                                                                required placeholder="Hasil yang dicapai">
                                                                        </div>
                                                                    <?php } ?>
                                                                    
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" name="nilai_how" class="btn btn-success">Simpan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php include('pages/kpi/k_modalEdithow.php'); ?>
                                                <?php include('pages/kpi/k_modalHapushow.php'); ?>

                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <?php include("pages/part/p_footer.php"); ?>
</body>

</html>