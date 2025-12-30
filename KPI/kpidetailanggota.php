<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/getUser.php';

    $id_sf = $_GET['id'];

    if (isset($_POST['submit'])) {
    $poin = $_POST['poin'];
    $bobot = $_POST['bobot'];
    $poin2 = $_POST['poin2'];
    $bobot2 = $_POST['bobot2'];

    $sql = "INSERT INTO tb_kpi (id_user,poin,bobot,poin2,bobot2)
                    VALUES ('$id_sf', '$poin','$bobot','$poin2','$bobot2')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin')</script>";
    }
}

    if (isset($_POST['update'])) {
    $ids = $_GET['id'];
    $poin = $_POST['poin'];
    $bobot = $_POST['bobot'];
    $idk = $_POST['idk'];

    $sql = "UPDATE tb_kpi SET poin='$poin' ,bobot=$bobot  WHERE id=$idk AND id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin" . $result . "')</script>";
    }
}

if (isset($_POST['update2'])) {
    $ids = $_GET['id'];
    $poin2 = $_POST['poin2'];
    $bobot2 = $_POST['bobot2'];
    $idk = $_POST['idk'];

    $sql = "UPDATE tb_kpi SET poin2='$poin2', bobot2=$bobot2 WHERE id=$idk AND id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin" . $result . "')</script>";
    }
}
// Handler untuk tambah what dengan indikator
if (isset($_POST['what_add'])) {
    $ids = $_GET['id'];
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuan']);
    $bobot = floatval($_POST['bobot']);
    $idkpi = intval($_POST['idkpi']);
    
    // Insert ke tb_whats (nilai default 0, akan diupdate saat penilaian)
    $sql = "INSERT INTO tb_whats (id_user, id_kpi, p_what, bobot, hasil, nilai, total, indikatorwhat) 
            VALUES ('$ids', '$idkpi', '$tujuan', '$bobot', '', 0, 0, '')";
    
    if (mysqli_query($conn, $sql)) {
        $id_what = mysqli_insert_id($conn);
        
        // Insert indikator-indikator
        if (isset($_POST['indikator_keterangan']) && isset($_POST['indikator_nilai'])) {
            $keterangans = $_POST['indikator_keterangan'];
            $nilais = $_POST['indikator_nilai'];
            
            for ($i = 0; $i < count($keterangans); $i++) {
                $ket = mysqli_real_escape_string($conn, $keterangans[$i]);
                $nil = floatval($nilais[$i]);
                $urutan = $i + 1;
                
                $sql_indikator = "INSERT INTO tb_indikator_whats (id_what, keterangan, nilai, urutan) 
                                  VALUES ('$id_what', '$ket', '$nil', '$urutan')";
                mysqli_query($conn, $sql_indikator);
            }
        }
        
         header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal menambah What')</script>";
    }
}

// Handler untuk penilaian what (pilih indikator)
if (isset($_POST['nilai_what'])) {
    $ids = $_GET['id'];
    $id_what = intval($_POST['idkpi']);
    $id_indikator = intval($_POST['nilaisi']); // ID indikator yang dipilih
    
    // Ambil nilai DAN keterangan dari indikator yang dipilih
    $sql_get = "SELECT nilai, keterangan FROM tb_indikator_whats WHERE id_indikator = $id_indikator";
    $result_get = mysqli_query($conn, $sql_get);
    $data = mysqli_fetch_assoc($result_get);
    $nilai = $data['nilai'];
    $keterangan = mysqli_real_escape_string($conn, $data['keterangan']);
    
    // Ambil bobot what
    $sql_bobot = "SELECT bobot FROM tb_whats WHERE id_what = $id_what";
    $result_bobot = mysqli_query($conn, $sql_bobot);
    $data_bobot = mysqli_fetch_assoc($result_bobot);
    $bobot = $data_bobot['bobot'];
    
    // Hitung total
    $total = number_format($nilai * $bobot / 100, 2);
    
    // Update tb_whats (nilai + keterangan di kolom hasil)
    $sql_update = "UPDATE tb_whats 
                   SET nilai = $nilai, hasil = '$keterangan', total = $total 
                   WHERE id_what = $id_what AND id_user = '$ids'";
    
    if (mysqli_query($conn, $sql_update)) {
         header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan penilaian')</script>";
    }
}
if (isset($_POST['what_edit'])) {
    $ids = $_GET['id'];
    $idw = intval($_POST['idkw']);
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuanw']);
    $bobot = floatval($_POST['bobotw']);
    
    // Update tb_whats
    $sql = "UPDATE tb_whats SET p_what='$tujuan', bobot=$bobot WHERE id_what=$idw AND id_user='$ids'";
    
    if (mysqli_query($conn, $sql)) {
        // Hapus indikator yang ditandai untuk dihapus
        if (isset($_POST['indikator_hapus']) && is_array($_POST['indikator_hapus'])) {
            foreach ($_POST['indikator_hapus'] as $id_hapus) {
                $id_hapus = intval($id_hapus);
                mysqli_query($conn, "DELETE FROM tb_indikator_whats WHERE id_indikator = $id_hapus");
            }
        }
        
        // Update atau insert indikator
        if (isset($_POST['indikator_keterangan']) && isset($_POST['indikator_nilai'])) {
            $keterangans = $_POST['indikator_keterangan'];
            $nilais = $_POST['indikator_nilai'];
            $ids_indikator = $_POST['indikator_id'];
            
            for ($i = 0; $i < count($keterangans); $i++) {
                $ket = mysqli_real_escape_string($conn, $keterangans[$i]);
                $nil = floatval($nilais[$i]);
                $id_indi = intval($ids_indikator[$i]);
                $urutan = $i + 1;
                
                if ($id_indi > 0) {
                    // Update indikator yang sudah ada
                    $sql_update = "UPDATE tb_indikator_whats 
                                   SET keterangan='$ket', nilai=$nil, urutan=$urutan 
                                   WHERE id_indikator=$id_indi";
                    mysqli_query($conn, $sql_update);
                } else {
                    // Insert indikator baru
                    $sql_insert = "INSERT INTO tb_indikator_whats (id_what, keterangan, nilai, urutan) 
                                   VALUES ($idw, '$ket', $nil, $urutan)";
                    mysqli_query($conn, $sql_insert);
                }
            }
        }
        
         header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate What')</script>";
    }
}
// Handler untuk tambah how dengan indikator
if (isset($_POST['how_add'])) {
    $ids = $_GET['id'];
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuan']);
    $bobot = floatval($_POST['bobot']);
    $idkpi = intval($_POST['idkpi']);
    
    // Insert ke tb_hows (nilai default 0, akan diupdate saat penilaian)
    $sql = "INSERT INTO tb_hows (id_user, id_kpi, p_how, bobot, hasil, nilai, total, indikatorhow) 
            VALUES ('$ids', '$idkpi', '$tujuan', '$bobot', '', 0, 0, '')";
    
    if (mysqli_query($conn, $sql)) {
        $id_how = mysqli_insert_id($conn);
        
        // Insert indikator-indikator
        if (isset($_POST['indikator_keterangan']) && isset($_POST['indikator_nilai'])) {
            $keterangans = $_POST['indikator_keterangan'];
            $nilais = $_POST['indikator_nilai'];
            
            for ($i = 0; $i < count($keterangans); $i++) {
                $ket = mysqli_real_escape_string($conn, $keterangans[$i]);
                $nil = floatval($nilais[$i]);
                $urutan = $i + 1;
                
                $sql_indikator = "INSERT INTO tb_indikator_hows (id_how, keterangan, nilai, urutan) 
                                  VALUES ('$id_how', '$ket', '$nil', '$urutan')";
                mysqli_query($conn, $sql_indikator);
            }
        }
        
         header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal menambah How')</script>";
    }
}

// Handler untuk edit how dengan indikator
if (isset($_POST['how_edit'])) {
    $ids = $_GET['id'];
    $idh = intval($_POST['idkh']);
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuanh']);
    $bobot = floatval($_POST['boboth']);
    
    // Update tb_hows
    $sql = "UPDATE tb_hows SET p_how='$tujuan', bobot=$bobot WHERE id_how=$idh AND id_user='$ids'";
    
    if (mysqli_query($conn, $sql)) {
        // Hapus indikator yang ditandai untuk dihapus
        if (isset($_POST['indikator_hapus']) && is_array($_POST['indikator_hapus'])) {
            foreach ($_POST['indikator_hapus'] as $id_hapus) {
                $id_hapus = intval($id_hapus);
                mysqli_query($conn, "DELETE FROM tb_indikator_hows WHERE id_indikator = $id_hapus");
            }
        }
        
        // Update atau insert indikator
        if (isset($_POST['indikator_keterangan']) && isset($_POST['indikator_nilai'])) {
            $keterangans = $_POST['indikator_keterangan'];
            $nilais = $_POST['indikator_nilai'];
            $ids_indikator = $_POST['indikator_id'];
            
            for ($i = 0; $i < count($keterangans); $i++) {
                $ket = mysqli_real_escape_string($conn, $keterangans[$i]);
                $nil = floatval($nilais[$i]);
                $id_indi = intval($ids_indikator[$i]);
                $urutan = $i + 1;
                
                if ($id_indi > 0) {
                    // Update indikator yang sudah ada
                    $sql_update = "UPDATE tb_indikator_hows 
                                   SET keterangan='$ket', nilai=$nil, urutan=$urutan 
                                   WHERE id_indikator=$id_indi";
                    mysqli_query($conn, $sql_update);
                } else {
                    // Insert indikator baru
                    $sql_insert = "INSERT INTO tb_indikator_hows (id_how, keterangan, nilai, urutan) 
                                   VALUES ($idh, '$ket', $nil, $urutan)";
                    mysqli_query($conn, $sql_insert);
                }
            }
        }
        
         header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate How')</script>";
    }
}

// Handler untuk penilaian how (pilih indikator)
if (isset($_POST['nilai_how'])) {
    $ids = $_GET['id'];
    $id_how = intval($_POST['idkpi']);
    $id_indikator = intval($_POST['nilaisi']); // ID indikator yang dipilih
    
    // Ambil nilai DAN keterangan dari indikator yang dipilih
    $sql_get = "SELECT nilai, keterangan FROM tb_indikator_hows WHERE id_indikator = $id_indikator";
    $result_get = mysqli_query($conn, $sql_get);
    $data = mysqli_fetch_assoc($result_get);
    $nilai = $data['nilai'];
    $keterangan = mysqli_real_escape_string($conn, $data['keterangan']);
    
    // Ambil bobot how
    $sql_bobot = "SELECT bobot FROM tb_hows WHERE id_how = $id_how";
    $result_bobot = mysqli_query($conn, $sql_bobot);
    $data_bobot = mysqli_fetch_assoc($result_bobot);
    $bobot = $data_bobot['bobot'];
    
    // Hitung total
    $total = number_format($nilai * $bobot / 100, 2);
    
    // Update tb_hows (nilai + keterangan di kolom hasil)
    $sql_update = "UPDATE tb_hows 
                   SET nilai = $nilai, hasil = '$keterangan', total = $total 
                   WHERE id_how = $id_how AND id_user = '$ids'";
    
    if (mysqli_query($conn, $sql_update)) {
         header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan penilaian')</script>";
    }
}

if (isset($_POST['how_hapus'])) {
    $ids = $_GET['id'];
    $idkpi = $_POST['idkhd'];

    $sql = "delete from tb_hows where id_how=$idkpi and id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin')</script>";
    }
}
if (isset($_POST['what_hapus'])) {
    $ids = $_GET['id'];
    $idkpi = $_POST['idkwd'];

    $sql = "delete from tb_whats where id_what=$idkpi and id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin')</script>";
    }
}
if (isset($_POST['update'])) {
    $ids = $_GET['id'];
    $idkpi = $_POST['idk'];
    $poinn = $_POST['poin'];
    $bobott = $_POST['bobot'];

    $sql = "UPDATE `tb_kpi` set poin = '$poinn', bobot = $bobott, where id=$idkpi and id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        echo "<script>alert('Berhasil, Edit Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Edit Poin')</script>";
    }
}
if (isset($_POST['update2'])) {
    $ids = $_GET['id'];
    $idkpi = $_POST['idk'];
    $poinn = $_POST['poin2'];
    $bobott = $_POST['bobot2'];

    $sql = "UPDATE `tb_kpi` set poin2 = '$poinn', bobot2 = $bobott, where id=$idkpi and id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        echo "<script>alert('Berhasil, Edit Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Edit Poin')</script>";
    }
}

} ?>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>KPI Digital</title><!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="KPI Digital">
    <meta name="author" content="Rvld">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="assets/css/adminlte.css">

    <link rel="stylesheet" type="text/css" href="assets/css/datatables/datatables.min.css" />

    <script type="text/javascript" src="datatables/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"><!-- jsvectormap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="kpianggota?id=<?= $_GET['id']; ?>"
                            class="nav-link">Kembali</a> </li>
                            <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="nav-link">Tambah Poin KPI</a> </li>
                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->

                <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i
                                data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i
                                data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a>
                    </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle"
                            data-bs-toggle="dropdown"> <img style="margin-top: -2px;" src="assets/img/profile.png"
                                class="user-image rounded-circle shadow" alt="User Image"> <span
                                class="d-none d-md-inline"><?php echo $username ?></span> </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end"> <!--begin::User Image-->
                            <li class="user-footer">
                                <center> <a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a>
                                </center>
                            </li> <!--end::Menu Footer-->
                        </ul>
                    </li> <!--end::User Menu Dropdown-->
                </ul> <!--end::End Navbar Links-->

            </div> <!--end::Container-->
        </nav>
        <?php include("pages/part/p_aside.php"); ?>
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

                    <?php
                    $sqlang = "SELECT * FROM tb_kpi WHERE id_user='$id_sf'";
                    $resulasft = mysqli_query($conn, $sqlang);
                    while ($hasil = mysqli_fetch_assoc($resulasft)) {
                        $idKPI = $hasil['id'];
                        $poin = $hasil['poin'];
                        $bobot = $hasil['bobot'];
                        $poin2 = $hasil['poin2'];
                        $bobot2 = $hasil['bobot2'];

                    
                        ?>
                        <?php include("pages/kpi/k_maindetailanggota.php");
                    } ?>
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->
        </main>
        <?php include("pages/part/p_footer.php"); ?>
        <?php include('pages/kpi/k_modalAdd.php');?>
    </div>
</body>

</html>