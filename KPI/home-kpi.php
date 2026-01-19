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

// Handler untuk tambah what dengan indikator (WHAT A dan WHAT B)
if (isset($_POST['what_add'])) {
    $ids = $_SESSION['id_user'];
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuan']);
    $bobot = floatval($_POST['bobot']);
    $idkpi = intval($_POST['idkpi']);
    $tipe_what = mysqli_real_escape_string($conn, $_POST['tipe_what']); // 'A' atau 'B'
    $target_omset = isset($_POST['target_omset']) ? floatval($_POST['target_omset']) : 0;
    
    // Insert ke tb_whats
    $sql = "INSERT INTO tb_whats (id_user, id_kpi, tipe_what, p_what, bobot, target_omset, hasil, nilai, total) 
            VALUES ('$ids', '$idkpi', '$tipe_what', '$tujuan', '$bobot', '$target_omset', '', 0, 0)";
    
    if (mysqli_query($conn, $sql)) {
        $id_what = mysqli_insert_id($conn);
        
        // Jika What A, insert indikator-indikator
        if ($tipe_what == 'A' && isset($_POST['indikator_keterangan']) && isset($_POST['indikator_nilai'])) {
            $keterangans = $_POST['indikator_keterangan'];
            $nilais = $_POST['indikator_nilai'];
            
            for ($i = 0; $i < count($keterangans); $i++) {
                if (!empty($keterangans[$i])) { // Pastikan tidak kosong
                    $ket = mysqli_real_escape_string($conn, $keterangans[$i]);
                    $nil = floatval($nilais[$i]);
                    $urutan = $i + 1;
                    
                    $sql_indikator = "INSERT INTO tb_indikator_whats (id_what, keterangan, nilai, urutan) 
                                      VALUES ('$id_what', '$ket', '$nil', '$urutan')";
                    mysqli_query($conn, $sql_indikator);
                }
            }
        }
        
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal menambah What: " . mysqli_error($conn) . "')</script>";
    }
}

// Handler untuk penilaian what (WHAT A dan WHAT B)
if (isset($_POST['nilai_what'])) {
    $ids = $_SESSION['id_user'];
    $id_what = intval($_POST['idkpi']);
    
    // Ambil data what untuk cek tipe
    $sql_what = "SELECT tipe_what, bobot, id_user FROM tb_whats WHERE id_what = $id_what";
    $result_what = mysqli_query($conn, $sql_what);
    $data_what = mysqli_fetch_assoc($result_what);
    $tipe_what = $data_what['tipe_what'];
    $bobot = $data_what['bobot'];
    
    // ===== TAMBAHAN BARU: CEK PEMILIK =====
    $is_owner = ($data_what['id_user'] == $ids);
    // ===== AKHIR TAMBAHAN =====
    
    if ($tipe_what == 'A') {
        // WHAT A: Ambil nilai dari indikator yang dipilih
        $id_indikator = intval($_POST['nilaisi']);
        
        $sql_get = "SELECT nilai, keterangan FROM tb_indikator_whats WHERE id_indikator = $id_indikator";
        $result_get = mysqli_query($conn, $sql_get);
        $data = mysqli_fetch_assoc($result_get);
        $nilai = $data['nilai'];
        $keterangan = mysqli_real_escape_string($conn, $data['keterangan']);
        
        // Hitung total
        $total = number_format($nilai * $bobot / 100, 2);
        
        // ===== UBAH BAGIAN UPDATE INI =====
        if ($is_owner) {
            // Karyawan menilai sendiri - RESET tracking
            $sql_update = "UPDATE tb_whats 
                           SET nilai = $nilai, 
                               hasil = '$keterangan', 
                               total = $total,
                               is_edited=0,
                               edited_by=NULL,
                               edited_at=NULL,
                               original_p_what=NULL,
                               original_bobot=NULL,
                               original_hasil=NULL,
                               original_nilai=NULL,
                               original_total=NULL
                           WHERE id_what = $id_what AND id_user = '$ids'";
        } else {
            $sql_update = "UPDATE tb_whats 
                           SET nilai = $nilai, hasil = '$keterangan', total = $total 
                           WHERE id_what = $id_what AND id_user = '$ids'";
        }
        
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
        $hasil_text = mysqli_real_escape_string($conn, $hasil_text);
        
        // ===== UBAH BAGIAN UPDATE INI =====
        if ($is_owner) {
            // Karyawan menilai sendiri - RESET tracking
            $sql_update = "UPDATE tb_whats 
                           SET target_omset = $target_omset, 
                               nilai = $nilai, 
                               hasil = '$hasil_text', 
                               total = $total,
                               is_edited=0,
                               edited_by=NULL,
                               edited_at=NULL,
                               original_p_what=NULL,
                               original_bobot=NULL,
                               original_hasil=NULL,
                               original_nilai=NULL,
                               original_total=NULL
                           WHERE id_what = $id_what AND id_user = '$ids'";
        } else {
            // Atasan menilai
            $sql_update = "UPDATE tb_whats 
                           SET target_omset = $target_omset, 
                               nilai = $nilai, 
                               hasil = '$hasil_text', 
                               total = $total 
                           WHERE id_what = $id_what AND id_user = '$ids'";
        }
        // ===== AKHIR PERUBAHAN =====
    }
    
    if (mysqli_query($conn, $sql_update)) {
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan penilaian: " . mysqli_error($conn) . "')</script>";
    }
}

// Handler untuk edit what (WHAT A dan WHAT B)
if (isset($_POST['what_edit'])) {
    $ids = $_SESSION['id_user'];
    $idw = intval($_POST['idkw']);
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuanw']);
    $bobot = floatval($_POST['bobotw']);
    
    // ===== TAMBAHAN BARU: CEK APAKAH YANG EDIT ADALAH PEMILIK =====
    $sql_check_owner = "SELECT id_user FROM tb_whats WHERE id_what = $idw";
    $result_owner = mysqli_query($conn, $sql_check_owner);
    $data_owner = mysqli_fetch_assoc($result_owner);
    
    $is_owner = ($data_owner['id_user'] == $ids);
    // ===== AKHIR TAMBAHAN =====
    
    // Ambil tipe what
    $sql_check = "SELECT tipe_what FROM tb_whats WHERE id_what = $idw";
    $result_check = mysqli_query($conn, $sql_check);
    $data_check = mysqli_fetch_assoc($result_check);
    $tipe_what = $data_check['tipe_what'];
    
    // ===== UBAH BAGIAN UPDATE INI =====
    if ($is_owner) {
        // Karyawan edit sendiri - RESET tracking
        $sql = "UPDATE tb_whats 
                SET p_what='$tujuan', 
                    bobot=$bobot,
                    is_edited=0,
                    edited_by=NULL,
                    edited_at=NULL,
                    original_p_what=NULL,
                    original_bobot=NULL,
                    original_hasil=NULL,
                    original_nilai=NULL,
                    original_total=NULL
                WHERE id_what=$idw AND id_user='$ids'";
    } else {
        // Atasan edit - tetap sama seperti sebelumnya
        $sql = "UPDATE tb_whats SET p_what='$tujuan', bobot=$bobot WHERE id_what=$idw AND id_user='$ids'";
    }
    
    if (mysqli_query($conn, $sql)) {
        // Jika What A, kelola indikator
        if ($tipe_what == 'A') {
            // ... (kode indikator tetap sama seperti sebelumnya)
            if (isset($_POST['indikator_hapus']) && is_array($_POST['indikator_hapus'])) {
                foreach ($_POST['indikator_hapus'] as $id_hapus) {
                    $id_hapus = intval($id_hapus);
                    mysqli_query($conn, "DELETE FROM tb_indikator_whats WHERE id_indikator = $id_hapus");
                }
            }
            
            if (isset($_POST['indikator_keterangan']) && isset($_POST['indikator_nilai']) && isset($_POST['indikator_id'])) {
                $keterangans = $_POST['indikator_keterangan'];
                $nilais = $_POST['indikator_nilai'];
                $ids_indikator = $_POST['indikator_id'];
                
                $count = min(count($keterangans), count($nilais), count($ids_indikator));
                
                for ($i = 0; $i < $count; $i++) {
                    if (!empty(trim($keterangans[$i])) && $nilais[$i] !== '') {
                        $ket = mysqli_real_escape_string($conn, trim($keterangans[$i]));
                        $nil = floatval($nilais[$i]);
                        $id_indi = intval($ids_indikator[$i]);
                        $urutan = $i + 1;
                        
                        if ($id_indi > 0) {
                            $sql_update = "UPDATE tb_indikator_whats 
                                           SET keterangan='$ket', nilai=$nil, urutan=$urutan 
                                           WHERE id_indikator=$id_indi";
                            mysqli_query($conn, $sql_update);
                        } else {
                            $sql_insert = "INSERT INTO tb_indikator_whats (id_what, keterangan, nilai, urutan) 
                                           VALUES ($idw, '$ket', $nil, $urutan)";
                            mysqli_query($conn, $sql_insert);
                        }
                    }
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
    $tipe_how = mysqli_real_escape_string($conn, $_POST['tipe_how']); // 'A' atau 'B'
    $target_omset = isset($_POST['target_omset']) ? floatval($_POST['target_omset']) : 0;
    
    // Insert ke tb_hows
    $sql = "INSERT INTO tb_hows (id_user, id_kpi, tipe_how, p_how, bobot, target_omset, hasil, nilai, total) 
            VALUES ('$ids', '$idkpi', '$tipe_how', '$tujuan', '$bobot', '$target_omset', '', 0, 0)";
    
    if (mysqli_query($conn, $sql)) {
        $id_how = mysqli_insert_id($conn);
        
        // Jika How A, insert indikator-indikator
        if ($tipe_how == 'A' && isset($_POST['indikator_keterangan']) && isset($_POST['indikator_nilai'])) {
            $keterangans = $_POST['indikator_keterangan'];
            $nilais = $_POST['indikator_nilai'];
            
            for ($i = 0; $i < count($keterangans); $i++) {
                if (!empty($keterangans[$i])) { // Pastikan tidak kosong
                    $ket = mysqli_real_escape_string($conn, $keterangans[$i]);
                    $nil = floatval($nilais[$i]);
                    $urutan = $i + 1;
                    
                    $sql_indikator = "INSERT INTO tb_indikator_hows (id_how, keterangan, nilai, urutan) 
                                      VALUES ('$id_how', '$ket', '$nil', '$urutan')";
                    mysqli_query($conn, $sql_indikator);
                }
            }
        }
        
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal menambah How: " . mysqli_error($conn) . "')</script>";
    }
}

// Handler untuk penilaian how (HOW A dan HOW B)
if (isset($_POST['nilai_how'])) {
    $ids = $_SESSION['id_user'];
    $id_how = intval($_POST['idkpi']);
    
    // Ambil data how untuk cek tipe
    $sql_how = "SELECT tipe_how, bobot, id_user FROM tb_hows WHERE id_how = $id_how";
    $result_how = mysqli_query($conn, $sql_how);
    $data_how = mysqli_fetch_assoc($result_how);
    $tipe_how = $data_how['tipe_how'];
    $bobot = $data_how['bobot'];
    
    // ===== TAMBAHAN BARU: CEK PEMILIK =====
    $is_owner = ($data_how['id_user'] == $ids);
    // ===== AKHIR TAMBAHAN =====
    
    if ($tipe_how == 'A') {
        // HOW A: Ambil nilai dari indikator yang dipilih
        $id_indikator = intval($_POST['nilaisi']);
        
        $sql_get = "SELECT nilai, keterangan FROM tb_indikator_hows WHERE id_indikator = $id_indikator";
        $result_get = mysqli_query($conn, $sql_get);
        $data = mysqli_fetch_assoc($result_get);
        $nilai = $data['nilai'];
        $keterangan = mysqli_real_escape_string($conn, $data['keterangan']);
        
        $total = number_format($nilai * $bobot / 100, 2);
        
        // ===== UBAH BAGIAN UPDATE INI =====
        if ($is_owner) {
            // Karyawan menilai sendiri - RESET tracking
            $sql_update = "UPDATE tb_hows 
                           SET nilai = $nilai, 
                               hasil = '$keterangan', 
                               total = $total,
                               is_edited=0,
                               edited_by=NULL,
                               edited_at=NULL,
                               original_p_how=NULL,
                               original_bobot=NULL,
                               original_hasil=NULL,
                               original_nilai=NULL,
                               original_total=NULL
                           WHERE id_how = $id_how AND id_user = '$ids'";
        } else {
            // Atasan menilai
            $sql_update = "UPDATE tb_hows 
                           SET nilai = $nilai, hasil = '$keterangan', total = $total 
                           WHERE id_how = $id_how AND id_user = '$ids'";
        }
        // ===== AKHIR PERUBAHAN =====
        
    } else {
        // HOW B: Hitung dari target omset dan hasil
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
        $hasil_text = mysqli_real_escape_string($conn, $hasil_text);
        
        // ===== UBAH BAGIAN UPDATE INI =====
        if ($is_owner) {
            // Karyawan menilai sendiri - RESET tracking
            $sql_update = "UPDATE tb_hows 
                           SET target_omset = $target_omset, 
                               nilai = $nilai, 
                               hasil = '$hasil_text', 
                               total = $total,
                               is_edited=0,
                               edited_by=NULL,
                               edited_at=NULL,
                               original_p_how=NULL,
                               original_bobot=NULL,
                               original_hasil=NULL,
                               original_nilai=NULL,
                               original_total=NULL
                           WHERE id_how = $id_how AND id_user = '$ids'";
        } else {
            // Atasan menilai
            $sql_update = "UPDATE tb_hows 
                           SET target_omset = $target_omset, 
                               nilai = $nilai, 
                               hasil = '$hasil_text', 
                               total = $total 
                           WHERE id_how = $id_how AND id_user = '$ids'";
        }
        // ===== AKHIR PERUBAHAN =====
    }
    
    if (mysqli_query($conn, $sql_update)) {
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan penilaian: " . mysqli_error($conn) . "')</script>";
    }
}

// Handler untuk edit how (HOW A dan HOW B)
if (isset($_POST['how_edit'])) {
    $ids = $_SESSION['id_user'];
    $idh = intval($_POST['idkh']);
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuanh']);
    $bobot = floatval($_POST['boboth']);
    
    // ===== TAMBAHAN BARU: CEK PEMILIK =====
    $sql_check_owner = "SELECT id_user FROM tb_hows WHERE id_how = $idh";
    $result_owner = mysqli_query($conn, $sql_check_owner);
    $data_owner = mysqli_fetch_assoc($result_owner);
    
    $is_owner = ($data_owner['id_user'] == $ids);
    // ===== AKHIR TAMBAHAN =====
    
    // Ambil tipe how
    $sql_check = "SELECT tipe_how FROM tb_hows WHERE id_how = $idh";
    $result_check = mysqli_query($conn, $sql_check);
    $data_check = mysqli_fetch_assoc($result_check);
    $tipe_how = $data_check['tipe_how'];
    
    // ===== UBAH BAGIAN UPDATE INI =====
    if ($is_owner) {
        // Karyawan edit sendiri - RESET tracking
        $sql = "UPDATE tb_hows 
                SET p_how='$tujuan', 
                    bobot=$bobot,
                    is_edited=0,
                    edited_by=NULL,
                    edited_at=NULL,
                    original_p_how=NULL,
                    original_bobot=NULL,
                    original_hasil=NULL,
                    original_nilai=NULL,
                    original_total=NULL
                WHERE id_how=$idh AND id_user='$ids'";
    } else {
        // Atasan edit
        $sql = "UPDATE tb_hows SET p_how='$tujuan', bobot=$bobot WHERE id_how=$idh AND id_user='$ids'";
    }
    // ===== AKHIR PERUBAHAN =====
    
    if (mysqli_query($conn, $sql)) {
        // Jika How A, kelola indikator (kode tetap sama)
        if ($tipe_how == 'A') {
            if (isset($_POST['indikator_hapus']) && is_array($_POST['indikator_hapus'])) {
                foreach ($_POST['indikator_hapus'] as $id_hapus) {
                    $id_hapus = intval($id_hapus);
                    mysqli_query($conn, "DELETE FROM tb_indikator_hows WHERE id_indikator = $id_hapus");
                }
            }
            
            if (isset($_POST['indikator_keterangan']) && isset($_POST['indikator_nilai'])) {
                $keterangans = $_POST['indikator_keterangan'];
                $nilais = $_POST['indikator_nilai'];
                $ids_indikator = $_POST['indikator_id'];
                
                for ($i = 0; $i < count($keterangans); $i++) {
                    if (!empty($keterangans[$i])) {
                        $ket = mysqli_real_escape_string($conn, $keterangans[$i]);
                        $nil = floatval($nilais[$i]);
                        $id_indi = intval($ids_indikator[$i]);
                        $urutan = $i + 1;
                        
                        if ($id_indi > 0) {
                            $sql_update = "UPDATE tb_indikator_hows 
                                           SET keterangan='$ket', nilai=$nil, urutan=$urutan 
                                           WHERE id_indikator=$id_indi";
                            mysqli_query($conn, $sql_update);
                        } else {
                            $sql_insert = "INSERT INTO tb_indikator_hows (id_how, keterangan, nilai, urutan) 
                                           VALUES ($idh, '$ket', $nil, $urutan)";
                            mysqli_query($conn, $sql_insert);
                        }
                    }
                }
            }
        }
        
        header('Location: home-kpi');
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate How')</script>";
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
if (isset($_POST['kpi_hapus'])) {
    $ids = $_SESSION['id_user'];
    $idkpi = intval($_POST['idkpi_hapus']);

    mysqli_query($conn, "DELETE iw FROM tb_indikator_whats iw 
                         INNER JOIN tb_whats w ON iw.id_what = w.id_what 
                         WHERE w.id_kpi = $idkpi AND w.id_user = '$ids'");
    
    mysqli_query($conn, "DELETE FROM tb_whats WHERE id_kpi = $idkpi AND id_user = '$ids'");
    
    mysqli_query($conn, "DELETE ih FROM tb_indikator_hows ih 
                         INNER JOIN tb_hows h ON ih.id_how = h.id_how 
                         WHERE h.id_kpi = $idkpi AND h.id_user = '$ids'");
    
    mysqli_query($conn, "DELETE FROM tb_hows WHERE id_kpi = $idkpi AND id_user = '$ids'");
    
    $sql = "DELETE FROM tb_kpi WHERE id = $idkpi AND id_user = '$ids'";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        header('Location: home-kpi');
        echo "<script>alert('Berhasil menghapus KPI')</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menghapus KPI')</script>";
    }
}
}
?>

<html lang="en">

<?php include("pages/part/p_header.php"); ?>

<style>
.edited-badge {
    background-color: #ffc107;
    color: #000;
    font-size: 9px;
    padding: 1px 4px;
    border-radius: 2px;
    margin-left: 3px;
    font-weight: bold;
}

.edited-row {
    background-color: #fff3cd !important;
    border-left: 3px solid #ffc107 !important;
}

.change-info {
    font-size: 10px;
    margin-top: 3px;
    padding: 3px 5px;
    background-color: #f8f9fa;
    border-radius: 3px;
    border-left: 2px solid #0d6efd;
}

.old-val {
    color: #dc3545;
    /* text-decoration: line-through; */
    font-weight: normal;
}

.new-val {
    color: #198754;
    font-weight: bold;
}

.change-timestamp {
    font-size: 9px;
    color: #6c757d;
    font-style: italic;
}
</style>

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