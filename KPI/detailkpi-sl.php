<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/simulasi-db/config.php';
require 'helper/simulasi-db/getUser.php';
require 'helper/simulasi-db/getKPI.php';

// Function to redirect with message
function redirectWithMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header('Location: home-kpi');
    exit();
}

// INSERT KPI
if (isset($_POST['submit'])) {
    $ids = $_SESSION['id_user'];
    $poin = $_POST['poin'];
    $bobot = $_POST['bobot'];
    $poin2 = $_POST['poin2'];
    $bobot2 = $_POST['bobot2'];

    $stmt = $conn->prepare("INSERT INTO tb_simulasi (id_user, poin, bobot, poin2, bobot2) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdsd", $ids, $poin, $bobot, $poin2, $bobot2);
    
    if ($stmt->execute()) {
        redirectWithMessage('Berhasil Tambah Poin');
    } else {
        redirectWithMessage('Gagal Tambah Poin', 'error');
    }
    $stmt->close();
}

// UPDATE KPI (poin & bobot)
if (isset($_POST['update'])) {
    $ids = $_SESSION['id_user'];
    $poin = $_POST['poin'];
    $bobot = $_POST['bobot'];
    $idk = $_POST['idk'];

    $stmt = $conn->prepare("UPDATE tb_simulasi SET poin=?, bobot=? WHERE id=? AND id_user=?");
    $stmt->bind_param("sdii", $poin, $bobot, $idk, $ids);
    
    if ($stmt->execute()) {
        redirectWithMessage('Berhasil Edit Poin');
    } else {
        redirectWithMessage('Gagal Edit Poin', 'error');
    }
    $stmt->close();
}

// UPDATE KPI (poin2 & bobot2)
if (isset($_POST['update2'])) {
    $ids = $_SESSION['id_user'];
    $poin2 = $_POST['poin2'];
    $bobot2 = $_POST['bobot2'];
    $idk = $_POST['idk'];

    $stmt = $conn->prepare("UPDATE tb_simulasi SET poin2=?, bobot2=? WHERE id=? AND id_user=?");
    $stmt->bind_param("sdii", $poin2, $bobot2, $idk, $ids);
    
    if ($stmt->execute()) {
        redirectWithMessage('Berhasil Edit Poin');
    } else {
        redirectWithMessage('Gagal Edit Poin', 'error');
    }
    $stmt->close();
}

// ADD WHAT
if (isset($_POST['what_add'])) {
    $ids = $_SESSION['id_user'];
    $tujuan = $_POST['tujuan'];
    $hasil = $_POST['hasil'];
    $nilai = $_POST['nilai'];
    $bobot = $_POST['bobot'];
    $idkpi = $_POST['idkpi'];
    $totalwhat = number_format($nilai * $bobot / 100, 2);
    $indikatorwhat = $_POST['indikatorwhat'];
    
    $stmt = $conn->prepare("INSERT INTO tb_whats VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisdsids", $ids, $idkpi, $tujuan, $bobot, $hasil, $nilai, $totalwhat, $indikatorwhat);
    
    if ($stmt->execute()) {
        redirectWithMessage('Berhasil Tambah What');
    } else {
        redirectWithMessage('Gagal Tambah What', 'error');
    }
    $stmt->close();
}

// EDIT WHAT
if (isset($_POST['what_edit'])) {
    $ids = $_SESSION['id_user'];
    $idw = $_POST['idkw'];
    $tujuan = $_POST['tujuanw'];
    $hasil = $_POST['hasilw'];
    $nilai = $_POST['nilaiw'];
    $bobot = $_POST['bobotw'];
    $total = number_format($nilai * $bobot / 100, 2);
    $indikatorwhat = $_POST['indikatorwhat'];
    
    $stmt = $conn->prepare("UPDATE tb_whats SET p_what=?, bobot=?, hasil=?, nilai=?, total=?, indikatorwhat=? WHERE id_what=?");
    $stmt->bind_param("sdsddsi", $tujuan, $bobot, $hasil, $nilai, $total, $indikatorwhat, $idw);
    
    if ($stmt->execute()) {
        redirectWithMessage('Berhasil Edit What');
    } else {
        redirectWithMessage('Gagal Edit What', 'error');
    }
    $stmt->close();
}

// DELETE WHAT
if (isset($_POST['what_hapus'])) {
    $ids = $_SESSION['id_user'];
    $idkpi = $_POST['idkwd'];

    $stmt = $conn->prepare("DELETE FROM tb_whats WHERE id_what=? AND id_user=?");
    $stmt->bind_param("ii", $idkpi, $ids);
    
    if ($stmt->execute()) {
        redirectWithMessage('Berhasil Hapus What');
    } else {
        redirectWithMessage('Gagal Hapus What', 'error');
    }
    $stmt->close();
}

// ADD HOW
if (isset($_POST['how_add'])) {
    $ids = $_SESSION['id_user'];
    $tujuan = $_POST['tujuan'];
    $hasil = $_POST['hasil'];
    $nilai = $_POST['nilai'];
    $bobot = $_POST['bobot'];
    $idkpi = $_POST['idkpi'];
    $totalhow = number_format($nilai * $bobot / 100, 2);
    $indikatorhow = $_POST['indikatorh'];
    
    $stmt = $conn->prepare("INSERT INTO tb_hows VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisdsids", $ids, $idkpi, $tujuan, $bobot, $hasil, $nilai, $totalhow, $indikatorhow);
    
    if ($stmt->execute()) {
        redirectWithMessage('Berhasil Tambah How');
    } else {
        redirectWithMessage('Gagal Tambah How', 'error');
    }
    $stmt->close();
}

// EDIT HOW
if (isset($_POST['how_edit'])) {
    $ids = $_SESSION['id_user'];
    $idw = $_POST['idkh'];
    $tujuan = $_POST['tujuanh'];
    $hasil = $_POST['hasilh'];
    $nilai = $_POST['nilaih'];
    $bobot = $_POST['boboth'];
    $total = number_format($nilai * $bobot / 100, 2);
    $indikator = $_POST['indikatorh'];

    $stmt = $conn->prepare("UPDATE tb_hows SET p_how=?, bobot=?, hasil=?, nilai=?, total=?, indikatorhow=? WHERE id_how=?");
    $stmt->bind_param("sdsddsi", $tujuan, $bobot, $hasil, $nilai, $total, $indikator, $idw);
    
    if ($stmt->execute()) {
        redirectWithMessage('Berhasil Edit How');
    } else {
        redirectWithMessage('Gagal Edit How', 'error');
    }
    $stmt->close();
}

// DELETE HOW
if (isset($_POST['how_hapus'])) {
    $ids = $_SESSION['id_user'];
    $idkpi = $_POST['idkhd'];

    $stmt = $conn->prepare("DELETE FROM tb_hows WHERE id_how=? AND id_user=?");
    $stmt->bind_param("ii", $idkpi, $ids);
    
    if ($stmt->execute()) {
        redirectWithMessage('Berhasil Hapus How');
    } else {
        redirectWithMessage('Gagal Hapus How', 'error');
    }
    $stmt->close();
}

// ADD WHAT INDICATOR
if (isset($_POST['whatindi_add'])) {
    $ids = $_SESSION['id_user'];
    $idkpi = $_POST['idkpiindikator'];
    $idwhat = $_POST['idwhatindikator'];
    $whatindikator = $_POST['whatindikator'];
    $nilaiindi = $_POST['nilaiindi'];
    
    $stmt = $conn->prepare("INSERT INTO tb_indikatorwhats VALUES (null, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisd", $ids, $idkpi, $idwhat, $whatindikator, $nilaiindi);
    
    if ($stmt->execute()) {
        redirectWithMessage('Berhasil Tambah Indikator');
    } else {
        redirectWithMessage('Gagal Tambah Indikator', 'error');
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/kpi/k_nav.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main">
            <div class="mt-3">
                <?php 
                // Display messages
                if (isset($_SESSION['message'])) {
                    $alertType = $_SESSION['message_type'] === 'error' ? 'danger' : 'success';
                    echo "<div class='alert alert-{$alertType} alert-dismissible fade show' role='alert'>";
                    echo htmlspecialchars($_SESSION['message']);
                    echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
                    echo "</div>";
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                }
                ?>
            </div>
            <div class="app-content">
                <div class="container-fluid" style="font-size:13px;">
                    <?php while ($hasil = mysqli_fetch_assoc($result)) {
                        $idKPI = $hasil['id'];
                        $poin = $hasil['poin'];
                        $bobot = $hasil['bobot'];
                        $poin2 = $hasil['poin2'];
                        $bobot2 = $hasil['bobot2'];
                    ?>
                    <?php include("pages/kpi/k_main.php");
                    } ?>
                </div>
            </div>
        </main>
        <?php include("pages/part/p_footer.php"); ?>
    </div>
</body>
</html>