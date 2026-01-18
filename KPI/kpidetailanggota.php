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
// Handler untuk tambah what dengan indikator (WHAT A dan WHAT B)
if (isset($_POST['what_add'])) {
    $ids = $_GET['id'];
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
        
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal menambah What: " . mysqli_error($conn) . "')</script>";
    }
}

// Handler untuk penilaian what (WHAT A dan WHAT B)
if (isset($_POST['nilai_what'])) {
    $ids = $_GET['id'];
    $id_what = intval($_POST['idkpi']);
    $editor_id = $_SESSION['id_user'];
    
    // Ambil data lama terlebih dahulu
    $sql_old = "SELECT hasil, nilai, total, tipe_what, bobot, p_what FROM tb_whats WHERE id_what = $id_what";
    $result_old = mysqli_query($conn, $sql_old);
    $data_old = mysqli_fetch_assoc($result_old);
    
    $tipe_what = $data_old['tipe_what'];
    $bobot = $data_old['bobot'];
    
    if ($tipe_what == 'A') {
        $id_indikator = intval($_POST['nilaisi']);
        
        $sql_get = "SELECT nilai, keterangan FROM tb_indikator_whats WHERE id_indikator = $id_indikator";
        $result_get = mysqli_query($conn, $sql_get);
        $data = mysqli_fetch_assoc($result_get);
        $nilai = $data['nilai'];
        $keterangan = mysqli_real_escape_string($conn, $data['keterangan']);
        
        $total = number_format($nilai * $bobot / 100, 2);
        
        // Update dengan menyimpan data lama
        $sql_update = "UPDATE tb_whats 
                       SET nilai = $nilai, 
                           hasil = '$keterangan', 
                           total = $total,
                           is_edited=1,
                           edited_by=$editor_id,
                           edited_at=NOW(),
                           original_p_what='" . mysqli_real_escape_string($conn, $data_old['p_what']) . "',
                           original_bobot=" . $data_old['bobot'] . ",
                           original_hasil='" . mysqli_real_escape_string($conn, $data_old['hasil']) . "',
                           original_nilai=" . ($data_old['nilai'] ? $data_old['nilai'] : 0) . ",
                           original_total=" . ($data_old['total'] ? $data_old['total'] : 0) . "
                       WHERE id_what = $id_what AND id_user = '$ids'";
        
    } else {
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
        
        // ===== PERBAIKAN: TAMBAHKAN original_target_omset =====
        $sql_get_original = "SELECT hasil, nilai, total, target_omset, original_hasil, original_nilai, original_total, original_target_omset 
                            FROM tb_whats WHERE id_what = $id_what";
        $result_original = mysqli_query($conn, $sql_get_original);
        $data_original = mysqli_fetch_assoc($result_original);

        // Jika belum pernah di-edit, simpan nilai original
        if (empty($data_original['original_hasil'])) {
            $original_hasil = mysqli_real_escape_string($conn, $data_original['hasil']);
            $original_nilai = $data_original['nilai'] ? $data_original['nilai'] : 0;
            $original_total = $data_original['total'] ? $data_original['total'] : 0;
            $original_target_omset = $data_original['target_omset'] ? $data_original['target_omset'] : 0; // TAMBAHAN BARU
        } else {
        // Jika sudah pernah di-edit, tetap gunakan original yang lama
            $original_hasil = $data_original['original_hasil'];
            $original_nilai = $data_original['original_nilai'];
            $original_total = $data_original['original_total'];
            $original_target_omset = $data_original['original_target_omset'] ? $data_original['original_target_omset'] : 0; // TAMBAHAN BARU
        }

        $sql_update = "UPDATE tb_whats 
                    SET target_omset = $target_omset, 
                        nilai = $nilai, 
                        hasil = '$hasil_text', 
                        total = $total,
                        is_edited = 1,
                        edited_by = $editor_id,
                        edited_at = NOW(),
                        original_hasil = '$original_hasil',
                        original_nilai = $original_nilai,
                        original_total = $original_total,
                        original_target_omset = $original_target_omset
                        WHERE id_what = $id_what AND id_user = '$ids'";
    }
    
    if (mysqli_query($conn, $sql_update)) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan penilaian: " . mysqli_error($conn) . "')</script>";
    }
}

// Handler untuk edit what (WHAT A dan WHAT B)
if (isset($_POST['what_edit'])) {
    $ids = $_GET['id'];
    $idw = intval($_POST['idkw']);
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuanw']);
    $bobot = floatval($_POST['bobotw']);
    $editor_id = $_SESSION['id_user'];
    
    // Ambil data what
    $sql_check = "SELECT tipe_what, p_what, bobot FROM tb_whats WHERE id_what = $idw";
    $result_check = mysqli_query($conn, $sql_check);
    $data_check = mysqli_fetch_assoc($result_check);
    $tipe_what = $data_check['tipe_what'];
    
    // ===== PERBAIKAN: CEK PERUBAHAN DAN SIMPAN ORIGINAL =====
    $sql_get_original = "SELECT p_what, bobot, original_p_what, original_bobot FROM tb_whats WHERE id_what=$idw";
    $result_original = mysqli_query($conn, $sql_get_original);
    $data_original = mysqli_fetch_assoc($result_original);
    
    // Cek apakah ada perubahan
    $ada_perubahan = ($data_original['p_what'] != $tujuan || $data_original['bobot'] != $bobot);
    
    if ($ada_perubahan) {
        // Jika belum pernah di-edit, simpan nilai original
        if (empty($data_original['original_p_what'])) {
            $original_p_what = mysqli_real_escape_string($conn, $data_original['p_what']);
            $original_bobot = $data_original['bobot'];
        } else {
            // Jika sudah pernah di-edit, tetap gunakan original yang lama
            $original_p_what = $data_original['original_p_what'];
            $original_bobot = $data_original['original_bobot'];
        }
        
        $sql = "UPDATE tb_whats 
                SET p_what='$tujuan', 
                    bobot=$bobot,
                    is_edited=1,
                    edited_by=$editor_id,
                    edited_at=NOW(),
                    original_p_what='$original_p_what',
                    original_bobot=$original_bobot
                WHERE id_what=$idw AND id_user='$ids'";
    } else {
        // Tidak ada perubahan
        $sql = "UPDATE tb_whats SET p_what='$tujuan', bobot=$bobot WHERE id_what=$idw AND id_user='$ids'";
    }
    // ===== AKHIR PERBAIKAN =====
    
    if (mysqli_query($conn, $sql)) {
        // Jika What A, kelola indikator (kode tetap sama seperti sebelumnya)
        if ($tipe_what == 'A') {
            // ... (kode indikator tidak berubah)
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
                            $sql_old_ind = "SELECT keterangan, nilai FROM tb_indikator_whats WHERE id_indikator=$id_indi";
                            $result_old_ind = mysqli_query($conn, $sql_old_ind);
                            $data_old_ind = mysqli_fetch_assoc($result_old_ind);
                            
                            $ada_perubahan_ind = ($data_old_ind['keterangan'] != $ket || $data_old_ind['nilai'] != $nil);
                            
                            if ($ada_perubahan_ind) {
                                $sql_update = "UPDATE tb_indikator_whats 
                                               SET keterangan='$ket', 
                                                   nilai=$nil, 
                                                   urutan=$urutan,
                                                   is_edited=1,
                                                   edited_by=$editor_id,
                                                   edited_at=NOW(),
                                                   original_keterangan='" . mysqli_real_escape_string($conn, $data_old_ind['keterangan']) . "',
                                                   original_nilai=" . $data_old_ind['nilai'] . "
                                               WHERE id_indikator=$id_indi";
                            } else {
                                $sql_update = "UPDATE tb_indikator_whats 
                                               SET keterangan='$ket', nilai=$nil, urutan=$urutan 
                                               WHERE id_indikator=$id_indi";
                            }
                            mysqli_query($conn, $sql_update);
                        } else {
                            $sql_insert = "INSERT INTO tb_indikator_whats 
                                          (id_what, keterangan, nilai, urutan, is_edited, edited_by, edited_at) 
                                           VALUES ($idw, '$ket', $nil, $urutan, 1, $editor_id, NOW())";
                            mysqli_query($conn, $sql_insert);
                        }
                    }
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
        
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal menambah How: " . mysqli_error($conn) . "')</script>";
    }
}

// Handler untuk penilaian how
if (isset($_POST['nilai_how'])) {
    $ids = $_GET['id'];
    $id_how = intval($_POST['idkpi']);
    $editor_id = $_SESSION['id_user'];
    
    // Ambil data lama
    $sql_old = "SELECT hasil, nilai, total, tipe_how, bobot, p_how FROM tb_hows WHERE id_how = $id_how";
    $result_old = mysqli_query($conn, $sql_old);
    $data_old = mysqli_fetch_assoc($result_old);
    
    $tipe_how = $data_old['tipe_how'];
    $bobot = $data_old['bobot'];
    
    if ($tipe_how == 'A') {
        $id_indikator = intval($_POST['nilaisi']);
        
        $sql_get = "SELECT nilai, keterangan FROM tb_indikator_hows WHERE id_indikator = $id_indikator";
        $result_get = mysqli_query($conn, $sql_get);
        $data = mysqli_fetch_assoc($result_get);
        $nilai = $data['nilai'];
        $keterangan = mysqli_real_escape_string($conn, $data['keterangan']);
        
        $total = number_format($nilai * $bobot / 100, 2);
        
        $sql_update = "UPDATE tb_hows 
                       SET nilai = $nilai, 
                           hasil = '$keterangan', 
                           total = $total,
                           is_edited=1,
                           edited_by=$editor_id,
                           edited_at=NOW(),
                           original_p_how='" . mysqli_real_escape_string($conn, $data_old['p_how']) . "',
                           original_bobot=" . $data_old['bobot'] . ",
                           original_hasil='" . mysqli_real_escape_string($conn, $data_old['hasil']) . "',
                           original_nilai=" . ($data_old['nilai'] ? $data_old['nilai'] : 0) . ",
                           original_total=" . ($data_old['total'] ? $data_old['total'] : 0) . "
                       WHERE id_how = $id_how AND id_user = '$ids'";
        
    } else {
        // HOW B: Hitung dari target omset dan hasil
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
        
        // ===== PERBAIKAN: TAMBAHKAN original_target_omset =====
        $sql_get_original = "SELECT hasil, nilai, total, target_omset, original_hasil, original_nilai, original_total, original_target_omset 
                            FROM tb_hows WHERE id_how = $id_how";
        $result_original = mysqli_query($conn, $sql_get_original);
        $data_original = mysqli_fetch_assoc($result_original);
        
        // Jika belum pernah di-edit, simpan nilai original
        if (empty($data_original['original_hasil'])) {
            $original_hasil = mysqli_real_escape_string($conn, $data_original['hasil']);
            $original_nilai = $data_original['nilai'] ? $data_original['nilai'] : 0;
            $original_total = $data_original['total'] ? $data_original['total'] : 0;
            $original_target_omset = $data_original['target_omset'] ? $data_original['target_omset'] : 0; // TAMBAHAN BARU
        } else {
            // Jika sudah pernah di-edit, tetap gunakan original yang lama
            $original_hasil = $data_original['original_hasil'];
            $original_nilai = $data_original['original_nilai'];
            $original_total = $data_original['original_total'];
            $original_target_omset = $data_original['original_target_omset'] ? $data_original['original_target_omset'] : 0; // TAMBAHAN BARU
        }
        
        $sql_update = "UPDATE tb_hows 
                    SET target_omset = $target_omset, 
                        nilai = $nilai, 
                        hasil = '$hasil_text', 
                        total = $total,
                        is_edited = 1,
                        edited_by = $editor_id,
                        edited_at = NOW(),
                        original_hasil = '$original_hasil',
                        original_nilai = $original_nilai,
                        original_total = $original_total,
                        original_target_omset = $original_target_omset
                    WHERE id_how = $id_how AND id_user = '$ids'";
        // ===== AKHIR PERBAIKAN =====
    }
    
    if (mysqli_query($conn, $sql_update)) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan penilaian: " . mysqli_error($conn) . "')</script>";
    }
}

// Handler untuk edit how
if (isset($_POST['how_edit'])) {
    $ids = $_GET['id'];
    $idh = intval($_POST['idkh']);
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuanh']);
    $bobot = floatval($_POST['boboth']);
    $editor_id = $_SESSION['id_user'];
    
    // Ambil SEMUA data lama
    $sql_old = "SELECT p_how, bobot, hasil, nilai, total, tipe_how FROM tb_hows WHERE id_how = $idh";
    $result_old = mysqli_query($conn, $sql_old);
    $data_old = mysqli_fetch_assoc($result_old);
    $tipe_how = $data_old['tipe_how'];
    
    $ada_perubahan = ($data_old['p_how'] != $tujuan || $data_old['bobot'] != $bobot);
    
    if ($ada_perubahan) {
        $sql = "UPDATE tb_hows 
                SET p_how='$tujuan', 
                    bobot=$bobot,
                    is_edited=1,
                    edited_by=$editor_id,
                    edited_at=NOW(),
                    original_p_how='" . mysqli_real_escape_string($conn, $data_old['p_how']) . "',
                    original_bobot=" . $data_old['bobot'] . ",
                    original_hasil='" . mysqli_real_escape_string($conn, $data_old['hasil']) . "',
                    original_nilai=" . ($data_old['nilai'] ? $data_old['nilai'] : 0) . ",
                    original_total=" . ($data_old['total'] ? $data_old['total'] : 0) . "
                WHERE id_how=$idh AND id_user='$ids'";
    } else {
        $sql = "UPDATE tb_hows SET p_how='$tujuan', bobot=$bobot WHERE id_how=$idh AND id_user='$ids'";
    }
    
    if (mysqli_query($conn, $sql)) {
        // Jika How A, kelola indikator
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
                            // Ambil data lama indikator
                            $sql_old_ind = "SELECT keterangan, nilai FROM tb_indikator_hows WHERE id_indikator=$id_indi";
                            $result_old_ind = mysqli_query($conn, $sql_old_ind);
                            $data_old_ind = mysqli_fetch_assoc($result_old_ind);
                            
                            // Cek ada perubahan
                            $ada_perubahan_ind = ($data_old_ind['keterangan'] != $ket || $data_old_ind['nilai'] != $nil);
                            
                            if ($ada_perubahan_ind) {
                                $sql_update = "UPDATE tb_indikator_hows 
                                               SET keterangan='$ket', 
                                                   nilai=$nil, 
                                                   urutan=$urutan,
                                                   is_edited=1,
                                                   edited_by=$editor_id,
                                                   edited_at=NOW(),
                                                   original_keterangan='" . mysqli_real_escape_string($conn, $data_old_ind['keterangan']) . "',
                                                   original_nilai=" . $data_old_ind['nilai'] . "
                                               WHERE id_indikator=$id_indi";
                            } else {
                                $sql_update = "UPDATE tb_indikator_hows 
                                               SET keterangan='$ket', nilai=$nil, urutan=$urutan 
                                               WHERE id_indikator=$id_indi";
                            }
                            mysqli_query($conn, $sql_update);
                        } else {
                            // Insert indikator baru
                            $sql_insert = "INSERT INTO tb_indikator_hows 
                                          (id_how, keterangan, nilai, urutan, is_edited, edited_by, edited_at) 
                                           VALUES ($idh, '$ket', $nil, $urutan, 1, $editor_id, NOW())";
                            mysqli_query($conn, $sql_insert);
                        }
                    }
                }
            }
        }
        
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate How')</script>";
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
    text-decoration: line-through;
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
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                                <?php
                                // Tentukan URL kembali berdasarkan parameter ref
                                $back_url = 'kpianggota?id=' . $_GET['id']; // default
                                ?>

                                <li class="nav-item d-none d-md-block"> 
                                    <a href="<?= $back_url ?>" class="nav-link">Kembali</a> 
                                </li>
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