<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';

// Ambil ID anggota dari parameter
$id_anggota = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_anggota == 0) {
    die("ID Anggota tidak valid");
}

// Ambil data anggota
$sql_anggota = "SELECT * FROM tb_users WHERE id = $id_anggota";
$result_anggota = mysqli_query($conn, $sql_anggota);
$data_anggota = mysqli_fetch_assoc($result_anggota);

if (!$data_anggota) {
    die("Data anggota tidak ditemukan");
}

// Fungsi untuk menghitung nilai
function getnilai($conn, $id)
{
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

    return $zboth + $zbotw;
}

function getkpi($nilair)
{
    if ($nilair < 90) {
        return "POOR";
    } elseif ($nilair <= 100) {
        return "GOOD";
    } elseif ($nilair <= 110) {
        return "Very Good";
    } else {
        return "Excellent";
    }
}

// Set nama file
$filename = 'Detail_KPI_' . str_replace(' ', '_', $data_anggota['nama_lngkp']) . '_' . date('Ymd_His') . '.xls';

// Set header untuk download Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Hitung nilai KPI
$nilai_kpi = getnilai($conn, $id_anggota);
$rating_kpi = getkpi($nilai_kpi);

// Tentukan warna
if ($nilai_kpi < 90) {
    $colorClass = "red";
} elseif ($nilai_kpi <= 100) {
    $colorClass = "orange";
} elseif ($nilai_kpi <= 110) {
    $colorClass = "green";
} else {
    $colorClass = "blue";
}

// Ambil bobot What dan How
$sql_bobot = "SELECT bobotwhat, bobothow FROM tb_bobotkpi WHERE id_user = $id_anggota";
$result_bobot = mysqli_query($conn, $sql_bobot);
$data_bobot = mysqli_fetch_assoc($result_bobot);
$bobot_what = $data_bobot['bobotwhat'] ?? 0;
$bobot_how = $data_bobot['bobothow'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #2C3E50;
            color: white;
            font-weight: bold;
        }
        .header-info {
            font-weight: bold;
            border: none;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            border: none;
        }
        .subtitle {
            font-size: 14px;
            font-weight: bold;
            background-color: #3498db;
            color: white;
        }
        .subtitle-success {
            font-size: 14px;
            font-weight: bold;
            background-color: #27ae60;
            color: white;
        }
        .center {
            text-align: center;
        }
        .right {
            text-align: right;
        }
        .red {
            color: red;
            font-weight: bold;
        }
        .orange {
            color: orange;
            font-weight: bold;
        }
        .green {
            color: green;
            font-weight: bold;
        }
        .blue {
            color: blue;
            font-weight: bold;
        }
        .bg-secondary {
            background-color: #95a5a6;
        }
        .indikator-row {
            background-color: #ecf0f1;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <!-- Header Info -->
    <table border="0">
        <tr>
            <td colspan="6" class="title">DETAIL KPI KARYAWAN</td>
        </tr>
        <tr>
            <td colspan="6" style="border: none;">&nbsp;</td>
        </tr>
        <tr>
            <td class="header-info">Nama</td>
            <td colspan="5" class="header-info">: <?= $data_anggota['nama_lngkp']; ?></td>
        </tr>
        <tr>
            <td class="header-info">NIK</td>
            <td colspan="5" class="header-info">: <?= $data_anggota['nik']; ?></td>
        </tr>
        <tr>
            <td class="header-info">Jabatan</td>
            <td colspan="5" class="header-info">: <?= $data_anggota['jabatan']; ?></td>
        </tr>
        <tr>
            <td class="header-info">Bagian</td>
            <td colspan="5" class="header-info">: <?= $data_anggota['bagian']; ?></td>
        </tr>
        <tr>
            <td class="header-info">Departemen</td>
            <td colspan="5" class="header-info">: <?= $data_anggota['departement']; ?></td>
        </tr>
        <tr>
            <td class="header-info">Atasan</td>
            <td colspan="5" class="header-info">: <?= $data_anggota['atasan']; ?></td>
        </tr>
        <tr>
            <td class="header-info">Tanggal Export</td>
            <td colspan="5" class="header-info">: <?= date('d/m/Y H:i:s'); ?></td>
        </tr>
        <tr>
            <td colspan="6" style="border: none;">&nbsp;</td>
        </tr>
    </table>

    <!-- Ringkasan Nilai KPI -->
    <table>
        <tr>
            <th colspan="6" class="center">RINGKASAN NILAI KPI</th>
        </tr>
        <tr>
            <td class="center" style="font-weight: bold;">Total Nilai KPI</td>
            <td class="center <?= $colorClass; ?>" style="font-size: 14pt;">
                <?= number_format($nilai_kpi, 2); ?>
            </td>
            <td class="center" style="font-weight: bold;">Rating</td>
            <td class="center <?= $colorClass; ?>" style="font-size: 14pt;">
                <?= $rating_kpi; ?>
            </td>
        </tr>
    </table>
    <br>

    <!-- Detail WHAT -->
    <table>
        <tr>
            <th colspan="6" class="subtitle center">DETAIL WHAT (TUJUAN)</th>
        </tr>
        <tr class="bg-secondary">
            <th class="center">No</th>
            <th>Poin What</th>
            <th class="center" width="10%">Bobot (%)</th>
            <th class="center" width="15%">Hasil Pencapaian</th>
            <th class="center" width="10%">Nilai</th>
            <th class="center" width="10%">Total</th>
        </tr>
        <?php
        $sql_kpi = "SELECT * FROM tb_kpi WHERE id_user = $id_anggota";
        $result_kpi = mysqli_query($conn, $sql_kpi);
        
        $no_what = 1;
        $total_bobot_what = 0;
        $total_nilai_what = 0;
        $total_nilai_what_akhir = 0;
        
        while ($kpi = mysqli_fetch_assoc($result_kpi)) {
            $id_kpi = $kpi['id'];
            $poin_what = $kpi['poin'];
            $bobot_what_kpi = $kpi['bobot'];
            
            // Ambil data what
            $sql_what = "SELECT * FROM tb_whats WHERE id_user = $id_anggota AND id_kpi = $id_kpi";
            $result_what = mysqli_query($conn, $sql_what);
            
            while ($what = mysqli_fetch_assoc($result_what)) {
                $id_what = $what['id_what'];
                $p_what = $what['p_what'];
                $bobot_what_item = $what['bobot'];
                $hasil_what = $what['hasil'];
                $nilai_what = $what['nilai'];
                $total_what = $what['total'];
                
                $total_bobot_what += $bobot_what_item;
                $total_nilai_what += $nilai_what;
                $total_nilai_what_akhir += $total_what;
                
                echo "<tr>";
                echo "<td class='center'>$no_what</td>";
                echo "<td><strong>$poin_what</strong><br>$p_what</td>";
                echo "<td class='center'>$bobot_what_item %</td>";
                echo "<td>$hasil_what</td>";
                echo "<td class='center'>$nilai_what</td>";
                echo "<td class='center'>" . number_format($total_what, 2) . "</td>";
                echo "</tr>";
                
                // Tampilkan indikator
                $sql_indikator = "SELECT * FROM tb_indikator_whats WHERE id_what = $id_what ORDER BY urutan";
                $result_indikator = mysqli_query($conn, $sql_indikator);
                
                if (mysqli_num_rows($result_indikator) > 0) {
                    echo "<tr class='indikator-row'>";
                    echo "<td></td>";
                    echo "<td colspan='5'><strong>Indikator Penilaian:</strong></td>";
                    echo "</tr>";
                    
                    while ($indikator = mysqli_fetch_assoc($result_indikator)) {
                        echo "<tr class='indikator-row'>";
                        echo "<td></td>";
                        echo "<td colspan='3'>&nbsp;&nbsp;&nbsp;• " . $indikator['keterangan'] . "</td>";
                        echo "<td class='center'>" . $indikator['nilai'] . "</td>";
                        echo "<td></td>";
                        echo "</tr>";
                    }
                }
                
                $no_what++;
            }
        }
        ?>
        <tr class="bg-secondary">
            <th colspan="2" class="center">TOTAL WHAT</th>
            <th class="center"><?= $total_bobot_what ?> %</th>
            <th></th>
            <th class="center"><?= number_format($total_nilai_what, 2) ?></th>
            <th class="center"><?= number_format($total_nilai_what_akhir, 2) ?></th>
        </tr>
        <tr>
            <th colspan="2" class="center">BOBOT WHAT</th>
            <th class="center"><?= $bobot_what ?> %</th>
            <th colspan="2" class="right">NILAI AKHIR WHAT:</th>
            <th class="center" style="font-weight: bold; font-size: 12pt;">
                <?= number_format($total_nilai_what_akhir * $bobot_what / 100, 2) ?>
            </th>
        </tr>
    </table>
    <br><br>

    <!-- Detail HOW -->
    <table>
        <tr>
            <th colspan="6" class="subtitle-success center">DETAIL HOW (CARA PENCAPAIAN)</th>
        </tr>
        <tr class="bg-secondary">
            <th class="center">No</th>
            <th>Poin How</th>
            <th class="center" width="10%">Bobot (%)</th>
            <th class="center" width="15%">Hasil Pencapaian</th>
            <th class="center" width="10%">Nilai</th>
            <th class="center" width="10%">Total</th>
        </tr>
        <?php
        $sql_kpi = "SELECT * FROM tb_kpi WHERE id_user = $id_anggota";
        $result_kpi = mysqli_query($conn, $sql_kpi);
        
        $no_how = 1;
        $total_bobot_how = 0;
        $total_nilai_how = 0;
        $total_nilai_how_akhir = 0;
        
        while ($kpi = mysqli_fetch_assoc($result_kpi)) {
            $id_kpi = $kpi['id'];
            $poin_how = $kpi['poin2'];
            $bobot_how_kpi = $kpi['bobot2'];
            
            // Ambil data how
            $sql_how = "SELECT * FROM tb_hows WHERE id_user = $id_anggota AND id_kpi = $id_kpi";
            $result_how = mysqli_query($conn, $sql_how);
            
            while ($how = mysqli_fetch_assoc($result_how)) {
                $id_how = $how['id_how'];
                $p_how = $how['p_how'];
                $bobot_how_item = $how['bobot'];
                $hasil_how = $how['hasil'];
                $nilai_how = $how['nilai'];
                $total_how = $how['total'];
                
                $total_bobot_how += $bobot_how_item;
                $total_nilai_how += $nilai_how;
                $total_nilai_how_akhir += $total_how;
                
                echo "<tr>";
                echo "<td class='center'>$no_how</td>";
                echo "<td><strong>$poin_how</strong><br>$p_how</td>";
                echo "<td class='center'>$bobot_how_item %</td>";
                echo "<td>$hasil_how</td>";
                echo "<td class='center'>$nilai_how</td>";
                echo "<td class='center'>" . number_format($total_how, 2) . "</td>";
                echo "</tr>";
                
                // Tampilkan indikator
                $sql_indikator = "SELECT * FROM tb_indikator_hows WHERE id_how = $id_how ORDER BY urutan";
                $result_indikator = mysqli_query($conn, $sql_indikator);
                
                if (mysqli_num_rows($result_indikator) > 0) {
                    echo "<tr class='indikator-row'>";
                    echo "<td></td>";
                    echo "<td colspan='5'><strong>Indikator Penilaian:</strong></td>";
                    echo "</tr>";
                    
                    while ($indikator = mysqli_fetch_assoc($result_indikator)) {
                        echo "<tr class='indikator-row'>";
                        echo "<td></td>";
                        echo "<td colspan='3'>&nbsp;&nbsp;&nbsp;• " . $indikator['keterangan'] . "</td>";
                        echo "<td class='center'>" . $indikator['nilai'] . "</td>";
                        echo "<td></td>";
                        echo "</tr>";
                    }
                }
                
                $no_how++;
            }
        }
        ?>
        <tr class="bg-secondary">
            <th colspan="2" class="center">TOTAL HOW</th>
            <th class="center"><?= $total_bobot_how ?> %</th>
            <th></th>
            <th class="center"><?= number_format($total_nilai_how, 2) ?></th>
            <th class="center"><?= number_format($total_nilai_how_akhir, 2) ?></th>
        </tr>
        <tr>
            <th colspan="2" class="center">BOBOT HOW</th>
            <th class="center"><?= $bobot_how ?> %</th>
            <th colspan="2" class="right">NILAI AKHIR HOW:</th>
            <th class="center" style="font-weight: bold; font-size: 12pt;">
                <?= number_format($total_nilai_how_akhir * $bobot_how / 100, 2) ?>
            </th>
        </tr>
    </table>
    <br><br>

    <!-- Keterangan -->
    <table border="0">
        <tr>
            <td colspan="6" style="font-weight: bold; border: none;">Keterangan Rating KPI:</td>
        </tr>
        <tr>
            <td class="red" style="border: none;">Poor</td>
            <td style="border: none;">: &lt; 90</td>
            <td colspan="4" style="border: none;"></td>
        </tr>
        <tr>
            <td class="orange" style="border: none;">Good</td>
            <td style="border: none;">: 90 - 100</td>
            <td colspan="4" style="border: none;"></td>
        </tr>
        <tr>
            <td class="green" style="border: none;">Very Good</td>
            <td style="border: none;">: 100 - 110</td>
            <td colspan="4" style="border: none;"></td>
        </tr>
        <tr>
            <td class="blue" style="border: none;">Excellent</td>
            <td style="border: none;">: &gt; 110</td>
            <td colspan="4" style="border: none;"></td>
        </tr>
    </table>
</body>
</html>