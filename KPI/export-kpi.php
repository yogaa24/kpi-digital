<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';

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

function getWhatt($conn, $id)
{
    $sql = "SELECT * FROM tb_kpi WHERE id_user='$id'";
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
    return $zbotw;
}

function getHoww($conn, $id)
{
    $sql = "SELECT * FROM tb_kpi WHERE id_user='$id'";
    $zboth = 0;
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
    return $zboth;
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
$filename = 'Laporan_KPI_Team_' . str_replace(' ', '_', $nama_lngkp) . '_' . date('Ymd_His') . '.xls';

// Set header untuk download Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Query data
$sqlhd = "SELECT *
FROM tb_users
WHERE atasan = '$nama_lngkp' OR nama_lngkp = '$nama_lngkp'
ORDER BY 
    CASE 
        WHEN jabatan = 'Kadep' THEN 1
        WHEN jabatan = 'Kabag' THEN 2
        WHEN jabatan = 'Karyawan' THEN 3
        ELSE 4
    END,
    nama_lngkp";

$sgdah = mysqli_query($conn, $sqlhd);
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
            text-align: center;
        }
        th {
            background-color: #2C3E50;
            color: white;
            font-weight: bold;
        }
        .header-info {
            font-weight: bold;
            text-align: left;
            border: none;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            border: none;
        }
        .left-align {
            text-align: left;
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
    </style>
</head>
<body>
    <!-- Header Info -->
    <table border="0">
        <tr>
            <td colspan="8" class="title">LAPORAN KPI TEAM</td>
        </tr>
        <tr>
            <td colspan="8" class="header-info">Nama Atasan: <?= $nama_lngkp; ?></td>
        </tr>
        <tr>
            <td colspan="8" class="header-info">Jabatan: <?= $jabatan; ?></td>
        </tr>
        <tr>
            <td colspan="8" class="header-info">Departemen: <?= $departement; ?></td>
        </tr>
        <tr>
            <td colspan="8" class="header-info">Tanggal Export: <?= date('d/m/Y H:i:s'); ?></td>
        </tr>
        <tr>
            <td colspan="8" style="border: none;">&nbsp;</td>
        </tr>
    </table>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Jabatan</th>
                <th>Bagian</th>
                <th>What</th>
                <th>How</th>
                <th>Nilai</th>
                <th>KPI</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($hasilsfa = mysqli_fetch_assoc($sgdah)) {
                $what = getWhatt($conn, $hasilsfa['id']);
                $how = getHoww($conn, $hasilsfa['id']);
                $nilai = getnilai($conn, $hasilsfa['id']);
                $kpi = getkpi($nilai);
                
                // Tentukan warna
                if ($nilai < 90) {
                    $colorClass = "red";
                } elseif ($nilai <= 100) {
                    $colorClass = "orange";
                } elseif ($nilai <= 110) {
                    $colorClass = "green";
                } else {
                    $colorClass = "blue";
                }
            ?>
            <tr>
                <td><?= $no; ?></td>
                <td class="left-align"><?= $hasilsfa['nama_lngkp']; ?></td>
                <td><?= $hasilsfa['jabatan']; ?></td>
                <td><?= $hasilsfa['bagian']; ?></td>
                <td><?= number_format($what, 2); ?></td>
                <td><?= number_format($how, 2); ?></td>
                <td class="<?= $colorClass; ?>"><?= number_format($nilai, 2); ?></td>
                <td class="<?= $colorClass; ?>"><?= $kpi; ?></td>
            </tr>
            <?php 
                $no++;
            } 
            ?>
        </tbody>
    </table>

    <!-- Keterangan -->
    <br>
    <table border="0">
        <tr>
            <td colspan="8" style="font-weight: bold; border: none;">Keterangan:</td>
        </tr>
        <tr>
            <td class="red" style="border: none;">Poor</td>
            <td style="border: none;">: &lt; 90</td>
            <td colspan="6" style="border: none;"></td>
        </tr>
        <tr>
            <td class="orange" style="border: none;">Good</td>
            <td style="border: none;">: 90 - 100</td>
            <td colspan="6" style="border: none;"></td>
        </tr>
        <tr>
            <td class="green" style="border: none;">Very Good</td>
            <td style="border: none;">: 100 - 110</td>
            <td colspan="6" style="border: none;"></td>
        </tr>
        <tr>
            <td class="blue" style="border: none;">Excellent</td>
            <td style="border: none;">: &gt; 110</td>
            <td colspan="6" style="border: none;"></td>
        </tr>
    </table>
</body>
</html>