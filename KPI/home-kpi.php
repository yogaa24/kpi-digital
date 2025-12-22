<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/getKPI.php';

if (isset($_POST['submit'])) {
    $ids = $_SESSION['id_user'];
    $poin = $_POST['poin'];
    $bobot = $_POST['bobot'];
    $poin2 = $_POST['poin2'];
    $bobot2 = $_POST['bobot2'];

    $sql = "INSERT INTO tb_kpi (id_user,poin,bobot,poin2,bobot2)
                    VALUES ('$ids', '$poin','$bobot','$poin2','$bobot2')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: home-kpi');
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin')</script>";
    }
}

if (isset($_POST['update'])) {
    $ids = $_SESSION['id_user'];
    $poin = $_POST['poin'];
    $bobot = $_POST['bobot'];
    $idk = $_POST['idk'];

    $sql = "UPDATE tb_kpi SET poin='$poin' ,bobot=$bobot  WHERE id=$idk AND id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: home-kpi');
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin" . $result . "')</script>";
    }
}
if (isset($_POST['update2'])) {
    $ids = $_SESSION['id_user'];
    $poin2 = $_POST['poin2'];
    $bobot2 = $_POST['bobot2'];
    $idk = $_POST['idk'];

    $sql = "UPDATE tb_kpi SET poin2='$poin2', bobot2=$bobot2 WHERE id=$idk AND id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: home-kpi');
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin" . $result . "')</script>";
    }
}

// Handler untuk tambah what dengan indikator
if (isset($_POST['what_add'])) {
    $ids = $_SESSION['id_user'];
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
        
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal menambah What')</script>";
    }
}

// Handler untuk penilaian what (pilih indikator)
if (isset($_POST['nilai_what'])) {
    $ids = $_SESSION['id_user'];
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
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan penilaian')</script>";
    }
}
if (isset($_POST['what_edit'])) {
    $ids = $_SESSION['id_user'];
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
        
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate What')</script>";
    }
}
// Handler untuk tambah how dengan indikator
if (isset($_POST['how_add'])) {
    $ids = $_SESSION['id_user'];
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
        
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal menambah How')</script>";
    }
}

// Handler untuk edit how dengan indikator
if (isset($_POST['how_edit'])) {
    $ids = $_SESSION['id_user'];
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
        
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate How')</script>";
    }
}

// Handler untuk penilaian how (pilih indikator)
if (isset($_POST['nilai_how'])) {
    $ids = $_SESSION['id_user'];
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
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan penilaian')</script>";
    }
}

if (isset($_POST['whatindi_add'])) {
    $ids = $_SESSION['id_user'];
    $idkpi = $_POST['idkpiindikator'];
    $idwhat = $_POST['idwhatindikator'];
    $whatindikator = $_POST['whatindikator'];
    $nilaiindi = $_POST['nilaiindi'];
    $sql = "INSERT INTO tb_indikatorwhats 
    VALUES (null, '$ids', '$idkpi','$idwhat','$whatindikator','$nilaiindi')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: home-kpi');
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin')</script>";
    } 
}
if (isset($_POST['how_hapus'])) {
    $ids = $_SESSION['id_user'];
    $idkpi = $_POST['idkhd'];

    $sql = "delete from tb_hows where id_how=$idkpi and id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: home-kpi');
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin')</script>";
    }
}
if (isset($_POST['what_hapus'])) {
    $ids = $_SESSION['id_user'];
    $idkpi = $_POST['idkwd'];

    $sql = "delete from tb_whats where id_what=$idkpi and id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: home-kpi');
        echo "<script>alert('Berhasil, Tambah Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Tambah Poin')</script>";
    }
}
if (isset($_POST['update'])) {
    $ids = $_SESSION['id_user'];
    $idkpi = $_POST['idk'];
    $poinn = $_POST['poin'];
    $bobott = $_POST['bobot'];

    $sql = "UPDATE `tb_kpi` set poin = '$poinn', bobot = $bobott where id=$idkpi and id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: home-kpi');
        echo "<script>alert('Berhasil, Edit Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Edit Poin')</script>";
    }
}
if (isset($_POST['update2'])) {
    $ids = $_SESSION['id_user'];
    $idkpi = $_POST['idk'];
    $poinn = $_POST['poin2'];
    $bobott = $_POST['bobot2'];

    $sql = "UPDATE `tb_kpi` set poin2 = '$poinn', bobot2 = $bobott where id=$idkpi and id_user=$ids";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: home-kpi');
        echo "<script>alert('Berhasil, Edit Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Edit Poin')</script>";
    }
}
}
?>

<html lang="en">

<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <?php include("pages/kpi/k_nav.php"); ?>
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
                <!--end::Container-->
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <?php include("pages/part/p_footer.php"); ?>
</body>

</html>