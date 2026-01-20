<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {
    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/getKPI.php';
    require 'helper/getHow.php';
    require 'helper/sp_functions.php';

    updateExpiredSP($conn);

    $user_id = isset($id_sf) ? $id_sf : $id_user;

    // PINDAHKAN BAGIAN INI KE ATAS (sebelum pengecekan archive)
    $blan = date('m/Y');
    $busd = explode('/', $blan);

    // Baru sekarang cek apakah bulan sebelumnya sudah di-archive
    $bulan_archive = str_pad($busd[0]-1, 2, '0', STR_PAD_LEFT);
    $tahun_archive = $busd[1];

    // Perbaikan untuk bulan Januari (bulan sebelumnya adalah Desember tahun lalu)
    if ($busd[0] == '01') {
        $bulan_archive = '12';
        $tahun_archive = $busd[1] - 1;
    }

    $periode_archive = $bulan_archive . '/' . $tahun_archive;

    $cek_archive = mysqli_query($conn, "SELECT id_archive FROM tbar_archive WHERE bulan = '$periode_archive' AND id_user = $id_user");
    $sudah_archive = mysqli_num_rows($cek_archive) > 0;

    require 'helper/verified_functions.php';

    // Cek status verified
    $bulan_sekarang = date('m/Y');
    $verified_status = checkKPIVerified($conn, $id_user, $bulan_sekarang);

    // Proses verify/unverify
    if (isset($_POST['verifyKPI'])) {
        $keterangan = $_POST['keterangan'] ?? '';
        if (verifyKPI($conn, $id_user, $id_user, $keterangan, $bulan_sekarang)) {
            echo "<script>
                alert('KPI berhasil diverifikasi!');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "?id=" . $id_user . "';
            </script>";
        } else {
            echo "<script>alert('Gagal memverifikasi KPI!');</script>";
        }
    }

    if (isset($_POST['unverifyKPI'])) {
        if (unverifyKPI($conn, $id_user, $bulan_sekarang)) {
            echo "<script>
                alert('Verifikasi KPI berhasil dibatalkan!');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "?id=" . $id_user . "';
            </script>";
        } else {
            echo "<script>alert('Gagal membatalkan verifikasi!');</script>";
        }
    }
    $zboth = 0;
    $zbotw = 0;

    $totalws = 0;
    $result = mysqli_query($conn, $sql);
    $resultsaf = mysqli_query($conn, $sql);
    while ($hasils = mysqli_fetch_assoc($resultsaf)) {
        $poin = $hasils['poin'];

        $sql3s = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id_user AND id_kpi=" . $hasils['id'];
        $result3s = mysqli_query($conn, $sql3s);
        $row3sd = mysqli_fetch_assoc($result3s);
        $totalnilaisd = $row3sd['total'];
        $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
        $totalws += $nilaiws;
    }
    $bobotkpid = 0;
    $sql5a = "SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user=$id_user";
    $result5a = mysqli_query($conn, $sql5a);
    while ($row5a = mysqli_fetch_assoc($result5a)) {
        $bobotkpid = $row5a['bw'];
    }
    $zbotw = ($totalws * $bobotkpid) / 100;
    // Hitung nilai dengan SP
    function getnilaiWithSPDisplay($conn, $id) {
        // Hitung nilai asli (copy dari fungsi getnilai yang sudah ada)
        $sql = "SELECT * FROM tb_kpi WHERE id_user='$id'";
        $zboth = 0;
        $zbotw = 0;

        $totalws = 0;
        $resultsaf = mysqli_query($conn, $sql);
        while ($hasils = mysqli_fetch_assoc($resultsaf)) {
            $sql3s = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id AND id_kpi=" . $hasils['id'];
            $result3s = mysqli_query($conn, $sql3s);
            $row3sd = mysqli_fetch_assoc($result3s);
            $totalnilaisd = $row3sd['total'];
            $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
            $totalws += $nilaiws;
        }
        $bobotkpid = 0;
        $sql5a = "SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user=$id";
        $result5a = mysqli_query($conn, $sql5a);
        while ($row5a = mysqli_fetch_assoc($result5a)) {
            $bobotkpid = $row5a['bw'];
        }
        $zbotw = ($totalws * $bobotkpid) / 100;
        
        $totalhfg = 0;
        $resultfg = mysqli_query($conn, $sql);
        while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
            $sql7fg = "SELECT SUM(total) as totalh FROM tb_hows WHERE id_user=$id AND id_kpi=" . $hasilfg['id'];
            $result7fg = mysqli_query($conn, $sql7fg);
            $row7fg = mysqli_fetch_assoc($result7fg);
            $totalnilaihfg = $row7fg['totalh'];
            $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
            $totalhfg += $nilaihfg;
        }
        $bobotkpias = 0;
        $sql8a = "SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user=$id";
        $result8a = mysqli_query($conn, $sql8a);
        while ($row8a = mysqli_fetch_assoc($result8a)) {
            $bobotkpias = $row8a['bh'];
        }
        $zboth = ($totalhfg * $bobotkpias) / 100;
        
        $nilai_asli = $zboth + $zbotw;
        
        // Kurangi dengan SP jika ada
        return calculateKPIWithSP($conn, $id, $nilai_asli);
    }
    // ===============================================================================
    $totalhfg = 0;
    $totalbobothfg = 0;
    $resultfg = mysqli_query($conn, $sql);

    while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
        $poin2fg = $hasilfg['poin2'];

        $sql7fg = "SELECT SUM(total) as totalh FROM tb_hows WHERE id_user=$id_user AND id_kpi=" . $hasilfg['id'];
        $result7fg = mysqli_query($conn, $sql7fg);
        $row7fg = mysqli_fetch_assoc($result7fg);
        $totalnilaihfg = $row7fg['totalh'];

        $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
        $totalhfg += $nilaihfg;
    }
    $bobotkpias = 0;
    $sql8a = "SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user=$id_user";
    $result8a = mysqli_query($conn, $sql8a);
    while ($row8a = mysqli_fetch_assoc($result8a)) {
        $bobotkpias = $row8a['bh'];
    }
    $zboth = ($totalhfg * $bobotkpias) / 100;

    if (isset($_POST['updateWhatB'])) {
        $bwasfg = $_POST['bobot'];
        $idfj = $_POST['idU'];
        $sql = "UPDATE `tb_bobotkpi` SET bobotwhat =" . $bwasfg . " where id_user =" . $idfj;
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
        } else {
            echo "<script>alert('Woops! Gagal update.')</script>";
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }
    }
    if (isset($_POST['updateHowB'])) {
        $bwasfg = $_POST['bobot'];
        $idfj = $_POST['idU'];
        $sql = "UPDATE `tb_bobotkpi` SET bobothow =" . $bwasfg . " where id_user =" . $idfj;
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
        } else {
            echo "<script>alert('Woops! Gagal update.')</script>";
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }
    }
    $blan = date('m/Y');
    $busd = explode('/', $blan);
    function tmapil($bl,$th){
        $bulannnn = '';

        if ($bl == '00') {
            $bulannnn = 'Desember ' . ($th - 1);
        }
        if ($bl == '01') {
            $bulannnn = 'Januari ' . $th;
        }
        if ($bl == '02') {
            $bulannnn = 'Februari ' . $th;
        }
        if ($bl == '03') {
            $bulannnn = 'Maret ' . $th;
        }
        if ($bl == '04') {
            $bulannnn = 'April ' . $th;
        }
        if ($bl == '05') {
            $bulannnn = 'Mei ' . $th;
        }
        if ($bl == '06') {
            $bulannnn = 'Juni ' . $th;
        }
        if ($bl == '07') {
            $bulannnn = 'Juli ' . $th;
        }
        if ($bl == '08') {
            $bulannnn = 'Agustus ' . $th;
        }
        if ($bl == '09') {
            $bulannnn = 'September ' . $th;
        }
        if ($bl == '10') {
            $bulannnn = 'Oktober ' . $th;
        }
        if ($bl == '11') {
            $bulannnn = 'November ' . $th;
        }
        if ($bl == '12') {
            $bulannnn = 'Desember ' . $th;
        }
        return $bulannnn;
    }
    

    if (isset($_POST['archiveNow'])) {
        $bulan_sekarang = date('m/Y');
        $verified_status = checkKPIVerified($conn, $id_user, $bulan_sekarang);
        
        if (!$verified_status) {
            echo "<script>
                alert('KPI belum diverifikasi oleh atasan! Archive tidak dapat dilakukan.');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
            exit();
        }
        // Cek ulang apakah sudah pernah archive
        $odkgh = $busd[0]-1;
        
        if ($odkgh == 0) {
            $odkgh = 12;
            $tahunArchive = $busd[1] - 1;
        } else {
            $tahunArchive = $busd[1];
        }
        
        $tgslk = str_pad($odkgh, 2, '0', STR_PAD_LEFT) . '/' . $tahunArchive;
        
        // Validasi: cek apakah sudah ada archive untuk periode ini
        $cek_existing = mysqli_query($conn, "SELECT id_archive FROM tbar_archive WHERE bulan = '$tgslk' AND id_user = $id_user");
        
        if (mysqli_num_rows($cek_existing) > 0) {
            echo "<script>
                alert('Archive untuk periode " . tmapil($odkgh, $tahunArchive) . " sudah pernah dibuat!');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
            exit();
        }
        
        $sqlksf = "INSERT INTO tbar_archive (bulan, id_user) VALUES ('$tgslk',$id_user)";
        $resuarc = mysqli_query($conn, $sqlksf);
        $last_id = mysqli_insert_id($conn);
    
        $resultrtyhj = mysqli_query($conn, "SELECT * FROM tbar_archive WHERE id_archive = $last_id and id_user = $id_user");
        $rosagw = mysqli_fetch_assoc($resultrtyhj);
        $idarcv = $rosagw['id_archive'];
    
        $panggilPoin = mysqli_query($conn,"Select * from tb_kpi where id_user = $id_user");
        while($ppPoin = mysqli_fetch_assoc($panggilPoin)){
            $addPoin = mysqli_query($conn,"INSERT INTO tbar_kpi (id_user,id_arcv,poin,bobot,poin2,bobot2) values ($id_user, $idarcv ,'".$ppPoin['poin']."', '".$ppPoin['bobot']."','".$ppPoin['poin2']."','".$ppPoin['bobot2']."')");
            $last_poin = mysqli_insert_id($conn);
    
            $panggilHow = mysqli_query($conn,"SELECT * FROM tb_hows WHERE id_user = $id_user AND id_kpi = ".$ppPoin['id']);
            while($howPoin = mysqli_fetch_assoc($panggilHow)){
                $addHow = mysqli_query($conn,"INSERT INTO tbar_hows (id_user,id_kpi,tipe_how,p_how,bobot,target_omset,hasil,nilai,total) VALUES ($id_user,$last_poin,'".$howPoin['tipe_how']."','".$howPoin['p_how']."','".$howPoin['bobot']."','".$howPoin['target_omset']."','".$howPoin['hasil']."','".$howPoin['nilai']."','".$howPoin['total']."')");
                
                // TAMBAHKAN KODE INI - Archive indikator hows
                $last_how_id = mysqli_insert_id($conn);
                $panggilIndikatorHow = mysqli_query($conn,"SELECT * FROM tb_indikator_hows WHERE id_how = ".$howPoin['id_how']." ORDER BY urutan");
                while($indHow = mysqli_fetch_assoc($panggilIndikatorHow)){
                    $addIndHow = mysqli_query($conn,"INSERT INTO tbar_indikator_hows (id_how, keterangan, nilai, urutan) VALUES ($last_how_id, '".mysqli_real_escape_string($conn, $indHow['keterangan'])."', '".$indHow['nilai']."', '".$indHow['urutan']."')");
                }
            }
    
            $panggilWhat = mysqli_query($conn,"SELECT * FROM tb_whats WHERE id_user = $id_user AND id_kpi = ".$ppPoin['id']);
            while($whatPoin = mysqli_fetch_assoc($panggilWhat)){
                $addWhat = mysqli_query($conn,"INSERT INTO tbar_whats (id_user,id_kpi,tipe_what,p_what,bobot,target_omset,hasil,nilai,total) VALUES ($id_user,$last_poin,'".$whatPoin['tipe_what']."','".$whatPoin['p_what']."','".$whatPoin['bobot']."','".$whatPoin['target_omset']."','".$whatPoin['hasil']."','".$whatPoin['nilai']."','".$whatPoin['total']."')");
                
                // TAMBAHKAN KODE INI - Archive indikator whats
                $last_what_id = mysqli_insert_id($conn);
                $panggilIndikatorWhat = mysqli_query($conn,"SELECT * FROM tb_indikator_whats WHERE id_what = ".$whatPoin['id_what']." ORDER BY urutan");
                while($indWhat = mysqli_fetch_assoc($panggilIndikatorWhat)){
                    $addIndWhat = mysqli_query($conn,"INSERT INTO tbar_indikator_whats (id_what, keterangan, nilai, urutan) VALUES ($last_what_id, '".mysqli_real_escape_string($conn, $indWhat['keterangan'])."', '".$indWhat['nilai']."', '".$indWhat['urutan']."')");
                }
            }
        }
        
        $panggilbobot = mysqli_query($conn,"Select * from tb_bobotkpi where id_user = $id_user");
        while($bobotPoin = mysqli_fetch_assoc($panggilbobot)){
            $addbobot = mysqli_query($conn,"INSERT INTO tbar_bobotkpi (id_user, id_arcv, bobotwhat, bobothow) values ($id_user,$idarcv, ".$bobotPoin['bobotwhat'].",".$bobotPoin['bobothow'].")");
        }
    }

    // Handler untuk simulasi KPI
    if (isset($_POST['simulateKPI'])) {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // 1. Hapus data simulasi lama (jika ada)
            mysqli_query($conn, "DELETE FROM tbsim_whats WHERE id_user = $id_user");
            mysqli_query($conn, "DELETE FROM tbsim_hows WHERE id_user = $id_user");
            mysqli_query($conn, "DELETE FROM tbsim_kpi WHERE id_user = $id_user");
            mysqli_query($conn, "DELETE FROM tbsim_bobotkpi WHERE id_user = $id_user");
            
            // 2. Copy KPI Points
            $kpi_real = mysqli_query($conn, "SELECT * FROM tb_kpi WHERE id_user = $id_user");
            $kpi_mapping = []; // Untuk mapping ID lama ke ID baru
            
            while ($kpi = mysqli_fetch_assoc($kpi_real)) {
                $poin = mysqli_real_escape_string($conn, $kpi['poin']);
                $bobot = $kpi['bobot'];
                $poin2 = mysqli_real_escape_string($conn, $kpi['poin2']);
                $bobot2 = $kpi['bobot2'];
                
                $sql_insert_kpi = "INSERT INTO tbsim_kpi (id_user, poin, bobot, poin2, bobot2) 
                                VALUES ($id_user, '$poin', $bobot, '$poin2', $bobot2)";
                mysqli_query($conn, $sql_insert_kpi);
                
                $new_kpi_id = mysqli_insert_id($conn);
                $kpi_mapping[$kpi['id']] = $new_kpi_id;
                
                // 3. Copy WHATS untuk KPI ini
                $whats_real = mysqli_query($conn, "SELECT * FROM tb_whats WHERE id_user = $id_user AND id_kpi = " . $kpi['id']);
                
                while ($what = mysqli_fetch_assoc($whats_real)) {
                    $tipe_what = mysqli_real_escape_string($conn, $what['tipe_what']);
                    $p_what = mysqli_real_escape_string($conn, $what['p_what']);
                    $bobot_what = $what['bobot'];
                    $target_omset = $what['target_omset'];
                    $hasil = mysqli_real_escape_string($conn, $what['hasil']);
                    $nilai = $what['nilai'];
                    $total = $what['total'];
                    
                    $sql_insert_what = "INSERT INTO tbsim_whats (id_user, id_kpi, tipe_what, p_what, bobot, target_omset, hasil, nilai, total) 
                                        VALUES ($id_user, $new_kpi_id, '$tipe_what', '$p_what', $bobot_what, $target_omset, '$hasil', $nilai, $total)";
                    mysqli_query($conn, $sql_insert_what);
                    
                    $new_what_id = mysqli_insert_id($conn);
                    
                    // Copy indikator whats (jika tipe A)
                    if ($what['tipe_what'] == 'A') {
                        $indikator_whats = mysqli_query($conn, "SELECT * FROM tb_indikator_whats WHERE id_what = " . $what['id_what'] . " ORDER BY urutan");
                        
                        while ($ind_what = mysqli_fetch_assoc($indikator_whats)) {
                            $keterangan = mysqli_real_escape_string($conn, $ind_what['keterangan']);
                            $nilai_ind = $ind_what['nilai'];
                            $urutan = $ind_what['urutan'];
                            
                            $sql_insert_ind = "INSERT INTO tbsim_indikator_whats (id_what, keterangan, nilai, urutan) 
                                            VALUES ($new_what_id, '$keterangan', $nilai_ind, $urutan)";
                            mysqli_query($conn, $sql_insert_ind);
                        }
                    }
                }
                
                // 4. Copy HOWS untuk KPI ini
                $hows_real = mysqli_query($conn, "SELECT * FROM tb_hows WHERE id_user = $id_user AND id_kpi = " . $kpi['id']);
                
                while ($how = mysqli_fetch_assoc($hows_real)) {
                    $tipe_how = mysqli_real_escape_string($conn, $how['tipe_how']);
                    $p_how = mysqli_real_escape_string($conn, $how['p_how']);
                    $bobot_how = $how['bobot'];
                    $target_omset = $how['target_omset'];
                    $hasil = mysqli_real_escape_string($conn, $how['hasil']);
                    $nilai = $how['nilai'];
                    $total = $how['total'];
                    
                    $sql_insert_how = "INSERT INTO tbsim_hows (id_user, id_kpi, tipe_how, p_how, bobot, target_omset, hasil, nilai, total) 
                                    VALUES ($id_user, $new_kpi_id, '$tipe_how', '$p_how', $bobot_how, $target_omset, '$hasil', $nilai, $total)";
                    mysqli_query($conn, $sql_insert_how);
                    
                    $new_how_id = mysqli_insert_id($conn);
                    
                    // Copy indikator hows (jika tipe A)
                    if ($how['tipe_how'] == 'A') {
                        $indikator_hows = mysqli_query($conn, "SELECT * FROM tb_indikator_hows WHERE id_how = " . $how['id_how'] . " ORDER BY urutan");
                        
                        while ($ind_how = mysqli_fetch_assoc($indikator_hows)) {
                            $keterangan = mysqli_real_escape_string($conn, $ind_how['keterangan']);
                            $nilai_ind = $ind_how['nilai'];
                            $urutan = $ind_how['urutan'];
                            
                            $sql_insert_ind = "INSERT INTO tbsim_indikator_hows (id_how, keterangan, nilai, urutan) 
                                            VALUES ($new_how_id, '$keterangan', $nilai_ind, $urutan)";
                            mysqli_query($conn, $sql_insert_ind);
                        }
                    }
                }
            }
            
            // 5. Copy Bobot KPI
            $bobot_real = mysqli_query($conn, "SELECT * FROM tb_bobotkpi WHERE id_user = $id_user");
            if ($bobot = mysqli_fetch_assoc($bobot_real)) {
                $bobotwhat = $bobot['bobotwhat'];
                $bobothow = $bobot['bobothow'];
                
                $sql_insert_bobot = "INSERT INTO tbsim_bobotkpi (id_user, bobotwhat, bobothow) 
                                    VALUES ($id_user, $bobotwhat, $bobothow)";
                mysqli_query($conn, $sql_insert_bobot);
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            echo "<script>
                alert('✅ Berhasil! Data KPI Real telah disalin ke KPI Simulasi');
                window.location.href = 'dashboard-simulasi';
            </script>";
            exit();
            
        } catch (Exception $e) {
            // Rollback jika ada error
            mysqli_rollback($conn);
            echo "<script>
                alert('❌ Gagal melakukan simulasi: " . $e->getMessage() . "');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
        }
    }
    if (isset($_POST['saveFeedback'])) {
        $feedback_text = mysqli_real_escape_string($conn, $_POST['feedback_text']);
        $bulan_feedback = date('m/Y');
        $pemberi_feedback = $id_user; // Yang login saat ini
        $penerima_feedback = $_POST['user_target']; // Target user
        
        // Cek apakah sudah ada feedback untuk bulan ini
        $cek_feedback = mysqli_query($conn, "SELECT id_feedback FROM tb_feedback WHERE 
            id_user_pemberi = $pemberi_feedback 
            AND id_user_penerima = $penerima_feedback 
            AND bulan = '$bulan_feedback'");
        
        if (mysqli_num_rows($cek_feedback) > 0) {
            // Update feedback yang sudah ada
            $update_feedback = mysqli_query($conn, "UPDATE tb_feedback SET 
                feedback = '$feedback_text',
                tanggal_update = NOW()
                WHERE id_user_pemberi = $pemberi_feedback 
                AND id_user_penerima = $penerima_feedback 
                AND bulan = '$bulan_feedback'");
            
            if ($update_feedback) {
                echo "<script>
                    alert('✅ Feedback berhasil diperbarui!');
                    window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                </script>";
            }
        } else {
            // Insert feedback baru
            $insert_feedback = mysqli_query($conn, "INSERT INTO tb_feedback 
                (id_user_pemberi, id_user_penerima, feedback, bulan, tanggal_buat) 
                VALUES ($pemberi_feedback, $penerima_feedback, '$feedback_text', '$bulan_feedback', NOW())");
            
            if ($insert_feedback) {
                echo "<script>
                    alert('✅ Feedback berhasil disimpan!');
                    window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                </script>";
            }
        }
    }

    // Fungsi untuk mendapatkan feedback
    function getFeedback($conn, $id_user_target, $bulan) {
        $feedback_data = [
            'self' => null,
            'atasan' => null
        ];
        
        // Feedback dari diri sendiri
        $self_feedback = mysqli_query($conn, "SELECT * FROM tb_feedback WHERE 
            id_user_pemberi = $id_user_target 
            AND id_user_penerima = $id_user_target 
            AND bulan = '$bulan'");
        
        if ($self_row = mysqli_fetch_assoc($self_feedback)) {
            $feedback_data['self'] = $self_row;
        }
        
        // Feedback dari atasan
        $atasan_feedback = mysqli_query($conn, "SELECT f.*, u.nama_lngkp as nama_pemberi 
            FROM tb_feedback f
            JOIN tb_users u ON f.id_user_pemberi = u.id
            WHERE f.id_user_penerima = $id_user_target 
            AND f.id_user_pemberi != $id_user_target 
            AND f.bulan = '$bulan'
            ORDER BY f.tanggal_buat DESC");
        
        if ($atasan_row = mysqli_fetch_assoc($atasan_feedback)) {
            $feedback_data['atasan'] = $atasan_row;
        }
        
        return $feedback_data;
    }
}

?>
<html lang="en">

<?php include("pages/part/p_header.php"); ?>
<style>
    .dropdown-item.disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.dropdown-item.disabled small {
    font-size: 10px;
    display: block;
    margin-top: 3px;
}
</style>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main">
            <div class="mt-3">
                <div class="app-content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php include("pages/dashboard/p_mainProfile.php"); ?>
                            <?php include("pages/dashboard/p_mainSummary.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include("pages/part/p_footer.php"); ?>
        
</body>

</html>