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

date_default_timezone_set('Asia/Jakarta');

// Ambil data anggota dengan prepared statement
$stmt = $conn->prepare("SELECT * FROM tb_users WHERE id = ?");
$stmt->bind_param("i", $id_anggota);
$stmt->execute();
$result_anggota = $stmt->get_result();
$data_anggota = $result_anggota->fetch_assoc();

if (!$data_anggota) {
    die("Data anggota tidak ditemukan");
}

// Fungsi untuk menghitung nilai dengan prepared statement
function getnilai($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM tb_kpi WHERE id_user = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $zboth = 0;
    $zbotw = 0;
    $totalws = 0;
    
    while ($hasils = $result->fetch_assoc()) {
        $stmt2 = $conn->prepare("SELECT SUM(total) as total FROM tb_whats WHERE id_user = ? AND id_kpi = ?");
        $stmt2->bind_param("ii", $id, $hasils['id']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $row = $result2->fetch_assoc();
        $totalnilaisd = $row['total'] ?? 0;
        $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
        $totalws += $nilaiws;
    }
    
    $stmt3 = $conn->prepare("SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user = ?");
    $stmt3->bind_param("i", $id);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    if ($row3 = $result3->fetch_assoc()) {
        $bobotkpid = $row3['bw'];
        $zbotw = ($totalws * $bobotkpid) / 100;
    }
    
    // Reset untuk How
    $stmt->execute();
    $result = $stmt->get_result();
    $totalhfg = 0;
    
    while ($hasilfg = $result->fetch_assoc()) {
        $stmt4 = $conn->prepare("SELECT SUM(total) as totalh FROM tb_hows WHERE id_user = ? AND id_kpi = ?");
        $stmt4->bind_param("ii", $id, $hasilfg['id']);
        $stmt4->execute();
        $result4 = $stmt4->get_result();
        $row4 = $result4->fetch_assoc();
        $totalnilaihfg = $row4['totalh'] ?? 0;
        $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
        $totalhfg += $nilaihfg;
    }
    
    $stmt5 = $conn->prepare("SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user = ?");
    $stmt5->bind_param("i", $id);
    $stmt5->execute();
    $result5 = $stmt5->get_result();
    if ($row5 = $result5->fetch_assoc()) {
        $bobotkpias = $row5['bh'];
        $zboth = ($totalhfg * $bobotkpias) / 100;
    }
    
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
    $colorClass = "blue";
} else {
    $colorClass = "green";
}

// Ambil bobot What dan How
$stmt_bobot = $conn->prepare("SELECT bobotwhat, bobothow FROM tb_bobotkpi WHERE id_user = ?");
$stmt_bobot->bind_param("i", $id_anggota);
$stmt_bobot->execute();
$result_bobot = $stmt_bobot->get_result();
$data_bobot = $result_bobot->fetch_assoc();
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
            vertical-align: top;
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
        .subtitle-what {
            font-size: 14px;
            font-weight: bold;
            background-color: #3498db;
            color: white;
        }
        .subtitle-how {
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
        .kpi-section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header Info -->
    <table border="0">
        <tr>
            <td colspan="12" class="title">DETAIL KPI KARYAWAN</td>
        </tr>
        <tr>
            <td colspan="12" style="border: none;">&nbsp;</td>
        </tr>
        <tr>
            <td class="header-info">Nama</td>
            <td colspan="11" class="header-info">: <?= htmlspecialchars($data_anggota['nama_lngkp']); ?></td>
        </tr>
        <tr>
            <td class="header-info">NIK</td>
            <td colspan="11" class="header-info">: <?= htmlspecialchars($data_anggota['nik']); ?></td>
        </tr>
        <tr>
            <td class="header-info">Jabatan</td>
            <td colspan="11" class="header-info">: <?= htmlspecialchars($data_anggota['jabatan']); ?></td>
        </tr>
        <tr>
            <td class="header-info">Bagian</td>
            <td colspan="11" class="header-info">: <?= htmlspecialchars($data_anggota['bagian']); ?></td>
        </tr>
        <tr>
            <td class="header-info">Departemen</td>
            <td colspan="11" class="header-info">: <?= htmlspecialchars($data_anggota['departement']); ?></td>
        </tr>
        <tr>
            <td class="header-info">Atasan</td>
            <td colspan="11" class="header-info">: <?= htmlspecialchars($data_anggota['atasan']); ?></td>
        </tr>
        <tr>
            <td class="header-info">Tanggal Export</td>
            <td colspan="11" class="header-info">: <?= date('d/m/Y H:i:s'); ?></td>
        </tr>
        <tr>
            <td colspan="12" style="border: none;">&nbsp;</td>
        </tr>
    </table>

    <!-- Ringkasan Nilai KPI -->
    <table>
        <tr>
            <th colspan="12" class="center">RINGKASAN NILAI KPI</th>
        </tr>
        <tr>
            <td colspan="3" class="center" style="font-weight: bold;">Total Nilai KPI</td>
            <td colspan="3" class="center <?= $colorClass; ?>" style="font-size: 14pt;">
                <?= number_format($nilai_kpi, 2); ?>
            </td>
            <td colspan="3" class="center" style="font-weight: bold;">Rating</td>
            <td colspan="3" class="center <?= $colorClass; ?>" style="font-size: 14pt;">
                <?= $rating_kpi; ?>
            </td>
        </tr>
    </table>
    <br>

    <?php
    // Ambil semua KPI
    $stmt_kpi = $conn->prepare("SELECT * FROM tb_kpi WHERE id_user = ?");
    $stmt_kpi->bind_param("i", $id_anggota);
    $stmt_kpi->execute();
    $result_kpi = $stmt_kpi->get_result();
    
    $kpi_number = 1;
    $grand_total_what = 0;
    $grand_total_how = 0;
    
    while ($kpi = $result_kpi->fetch_assoc()) {
        $id_kpi = $kpi['id'];
        $poin_what = htmlspecialchars($kpi['poin']);
        $bobot_what_kpi = $kpi['bobot'];
        $poin_how = htmlspecialchars($kpi['poin2']);
        $bobot_how_kpi = $kpi['bobot2'];
        
        // Ambil data What untuk KPI ini
        $stmt_what = $conn->prepare("SELECT * FROM tb_whats WHERE id_user = ? AND id_kpi = ?");
        $stmt_what->bind_param("ii", $id_anggota, $id_kpi);
        $stmt_what->execute();
        $result_what = $stmt_what->get_result();
        $whats = [];
        while ($w = $result_what->fetch_assoc()) {
            $whats[] = $w;
        }
        
        // Ambil data How untuk KPI ini
        $stmt_how = $conn->prepare("SELECT * FROM tb_hows WHERE id_user = ? AND id_kpi = ?");
        $stmt_how->bind_param("ii", $id_anggota, $id_kpi);
        $stmt_how->execute();
        $result_how = $stmt_how->get_result();
        $hows = [];
        while ($h = $result_how->fetch_assoc()) {
            $hows[] = $h;
        }
        
        echo "<table class='kpi-section'>";
        echo "<tr>";
        echo "<th colspan='12' class='center' style='background-color: #34495e; font-size: 14px;'>KPI #$kpi_number</th>";
        echo "</tr>";
        echo "<tr>";
        echo "<th colspan='6' class='subtitle-what center'>WHAT (Bobot: $bobot_what_kpi%)</th>";
        echo "<th colspan='6' class='subtitle-how center'>HOW (Bobot: $bobot_how_kpi%)</th>";
        echo "</tr>";
        echo "<tr>";
        echo "<td colspan='6' style='font-weight: bold; background-color: #e8f4f8;'>$poin_what</td>";
        echo "<td colspan='6' style='font-weight: bold; background-color: #e8f5e9;'>$poin_how</td>";
        echo "</tr>";
        
        // Header tabel
        echo "<tr class='bg-secondary'>";
        echo "<th class='center' width='3%'>No</th>";
        echo "<th width='20%'>Poin What</th>";
        echo "<th class='center' width='7%'>Bobot</th>";
        echo "<th width='10%'>Hasil</th>";
        echo "<th class='center' width='5%'>Nilai</th>";
        echo "<th class='center' width='5%'>Total</th>";
        
        echo "<th class='center' width='3%'>No</th>";
        echo "<th width='20%'>Poin How</th>";
        echo "<th class='center' width='7%'>Bobot</th>";
        echo "<th width='10%'>Hasil</th>";
        echo "<th class='center' width='5%'>Nilai</th>";
        echo "<th class='center' width='5%'>Total</th>";
        echo "</tr>";
        
        // Isi data What dan How berdampingan
        $max_rows = max(count($whats), count($hows));
        $no_what = 1;
        $no_how = 1;
        $subtotal_what = 0;
        $subtotal_how = 0;
        
        for ($i = 0; $i < $max_rows; $i++) {
            // Data What
            if (isset($whats[$i])) {
                $what = $whats[$i];
                $id_what = $what['id_what'];
                $p_what = htmlspecialchars($what['p_what']);
                $bobot_what_item = $what['bobot'];
                $hasil_what = htmlspecialchars($what['hasil']);
                $nilai_what = $what['nilai'];
                $total_what = $what['total'];
                $subtotal_what += $total_what;
                
                echo "<tr>";
                echo "<td class='center'>$no_what</td>";
                echo "<td>$p_what</td>";
                echo "<td class='center'>$bobot_what_item%</td>";
                echo "<td>$hasil_what</td>";
                echo "<td class='center'>$nilai_what</td>";
                echo "<td class='center'>" . number_format($total_what, 2) . "</td>";
            } else {
                echo "<tr>";
                echo "<td colspan='6'></td>";
            }
            
            // Data How
            if (isset($hows[$i])) {
                $how = $hows[$i];
                $id_how = $how['id_how'];
                $p_how = htmlspecialchars($how['p_how']);
                $bobot_how_item = $how['bobot'];
                $hasil_how = htmlspecialchars($how['hasil']);
                $nilai_how = $how['nilai'];
                $total_how = $how['total'];
                $subtotal_how += $total_how;
                
                echo "<td class='center'>$no_how</td>";
                echo "<td>$p_how</td>";
                echo "<td class='center'>$bobot_how_item%</td>";
                echo "<td>$hasil_how</td>";
                echo "<td class='center'>$nilai_how</td>";
                echo "<td class='center'>" . number_format($total_how, 2) . "</td>";
                echo "</tr>";
            } else {
                echo "<td colspan='6'></td>";
                echo "</tr>";
            }
            
            if (isset($whats[$i])) $no_what++;
            if (isset($hows[$i])) $no_how++;
        }
        
        // Subtotal untuk KPI ini
        $nilai_what_kpi = ($subtotal_what * $bobot_what_kpi) / 100;
        $nilai_how_kpi = ($subtotal_how * $bobot_how_kpi) / 100;
        $grand_total_what += $nilai_what_kpi;
        $grand_total_how += $nilai_how_kpi;
        
        echo "<tr class='bg-secondary'>";
        echo "<th colspan='5' class='right'>Subtotal What:</th>";
        echo "<th class='center' style='font-weight: bold;'>" . number_format($nilai_what_kpi, 2) . "</th>";
        echo "<th colspan='5' class='right'>Subtotal How:</th>";
        echo "<th class='center' style='font-weight: bold;'>" . number_format($nilai_how_kpi, 2) . "</th>";
        echo "</tr>";
        
        echo "</table>";
        echo "<br>";
        
        $kpi_number++;
    }
    ?>

    <!-- Total Akhir -->
    <table>
        <tr>
            <th colspan="6" class="subtitle-what center">TOTAL WHAT (Bobot: <?= $bobot_what ?>%)</th>
            <th colspan="6" class="subtitle-how center">TOTAL HOW (Bobot: <?= $bobot_how ?>%)</th>
        </tr>
        <tr>
            <td colspan="3" class="right" style="font-weight: bold;">Total Nilai What:</td>
            <td colspan="3" class="center" style="font-weight: bold; font-size: 12pt;">
                <?= number_format($grand_total_what, 2) ?>
            </td>
            <td colspan="3" class="right" style="font-weight: bold;">Total Nilai How:</td>
            <td colspan="3" class="center" style="font-weight: bold; font-size: 12pt;">
                <?= number_format($grand_total_how, 2) ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="right" style="font-weight: bold;">Nilai Akhir What:</td>
            <td colspan="3" class="center" style="font-weight: bold; font-size: 12pt; background-color: #e8f4f8;">
                <?= number_format($grand_total_what * $bobot_what / 100, 2) ?>
            </td>
            <td colspan="3" class="right" style="font-weight: bold;">Nilai Akhir How:</td>
            <td colspan="3" class="center" style="font-weight: bold; font-size: 12pt; background-color: #e8f5e9;">
                <?= number_format($grand_total_how * $bobot_how / 100, 2) ?>
            </td>
        </tr>
        <tr class="bg-secondary">
            <th colspan="6" class="right">TOTAL NILAI KPI KESELURUHAN:</th>
            <th colspan="6" class="center <?= $colorClass; ?>" style="font-size: 14pt;">
                <?= number_format($nilai_kpi, 2) ?> (<?= $rating_kpi ?>)
            </th>
        </tr>
    </table>
    <br><br>

    <!-- Keterangan -->
    <table border="0">
        <tr>
            <td colspan="12" style="font-weight: bold; border: none;">Keterangan Rating KPI:</td>
        </tr>
        <tr>
            <td class="red" style="border: none;">Poor</td>
            <td style="border: none;">: &lt; 90</td>
            <td colspan="10" style="border: none;"></td>
        </tr>
        <tr>
            <td class="orange" style="border: none;">Good</td>
            <td style="border: none;">: 90 - 100</td>
            <td colspan="10" style="border: none;"></td>
        </tr>
        <tr>
            <td class="green" style="border: none;">Very Good</td>
            <td style="border: none;">: 100 - 110</td>
            <td colspan="10" style="border: none;"></td>
        </tr>
        <tr>
            <td class="blue" style="border: none;">Excellent</td>
            <td style="border: none;">: &gt; 110</td>
            <td colspan="10" style="border: none;"></td>
        </tr>
    </table>
</body>
</html>