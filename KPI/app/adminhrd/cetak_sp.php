<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require_once __DIR__ . '/../../helper/config.php';
require_once __DIR__ . '/../../helper/sp_functions.php';

$id_sp = isset($_GET['id_sp']) ? intval($_GET['id_sp']) : 0;
if ($id_sp <= 0) {
    echo "ID Surat Peringatan tidak valid.";
    exit();
}

// Ambil data SP beserta data karyawan
$sql = "SELECT sp.*, u.nama_lngkp, u.nik, u.departement, u.jabatan, u.bagian 
        FROM tb_surat_peringatan sp
        JOIN tb_users u ON sp.id_user = u.id
        WHERE sp.id_sp = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_sp);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sp = mysqli_fetch_assoc($result);

if (!$sp) {
    echo "Data Surat Peringatan tidak ditemukan.";
    exit();
}

// Cek hak akses: hanya Admin HRD, Direktur, atau karyawan yang bersangkutan
$current_user_id = $_SESSION['id_user'];
$can_view = false;

// Ambil jabatan user saat ini
$q_user = mysqli_query($conn, "SELECT jabatan FROM tb_users WHERE id = '$current_user_id'");
$user_curr = mysqli_fetch_assoc($q_user);
$curr_jabatan = $user_curr['jabatan'] ?? '';

if ($current_user_id == $sp['id_user'] || in_array($curr_jabatan, ['Admin HRD', 'Direktur', 'Direktur Utama', 'Manager HRD', 'Kadep HRD'])) {
    $can_view = true;
}

if (!$can_view) {
    echo "Anda tidak memiliki akses untuk melihat dokumen ini.";
    exit();
}

// Nilai default untuk data yang belum terisi
$nomor_sp = !empty($sp['nomor_sp']) ? $sp['nomor_sp'] : '294/KIU-HRD/IX/' . date('Y', strtotime($sp['tanggal_sp']));
$tanggal_sp_indo = formatTanggalIndo($sp['tanggal_sp']);
$tanggal_kejadian = !empty($sp['tanggal_kejadian']) ? $sp['tanggal_kejadian'] : $sp['tanggal_sp'];
$tanggal_kejadian_indo = formatTanggalIndo($tanggal_kejadian);

$raw_aturan = !empty($sp['aturan_dilanggar']) ? $sp['aturan_dilanggar'] : "Peraturan Perusahaan Pasal 22 ayat (2) point 23 :\nTidak berhati-hati dan / atau lalai dalam melaksanakan tugas sehingga dapat mengakibatkan kerugiaan bagi perusahaan";
$raw_aturan = str_replace(["\r\n", "\\r\\n", "\\n", "\\r", "<br>", "<br/>", "<br />"], "\n", $raw_aturan);
$lines_aturan = explode("\n", $raw_aturan);
$aturan_title = trim($lines_aturan[0] ?? '');
$aturan_detail = trim(implode("\n", array_slice($lines_aturan, 1)));

$raw_alasan = !empty($sp['alasan']) ? $sp['alasan'] : 'Melakukan kelalaian dalam pelaksanaan tugas.';
$raw_alasan = str_replace(["\r\n", "\\r\\n", "\\n", "\\r"], "\n", $raw_alasan);

$raw_alasan_2 = !empty($sp['alasan_2']) ? $sp['alasan_2'] : '';
$raw_alasan_2 = str_replace(["\r\n", "\\r\\n", "\\n", "\\r"], "\n", $raw_alasan_2);

$penandatangan = !empty($sp['penandatangan']) ? $sp['penandatangan'] : 'Riza Dwi Fitrianingtyas';
$jabatan_penandatangan = !empty($sp['jabatan_penandatangan']) ? $sp['jabatan_penandatangan'] : 'Kepala Departemen HRD';
$tembusan = !empty($sp['tembusan']) ? $sp['tembusan'] : '1. Direktur sebagai laporan; 2. Kepala Departemen HRD; 3. Arsip;';

$sp_title = getSPFullTitle($sp['jenis_sp']);
$sp_ketentuan = getSPKetentuanText($sp['jenis_sp']);

// Path logo
$logo_path = 'assets/img/logo_karisma.png';
$logo_exists = file_exists(__DIR__ . '/../../' . $logo_path);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Peringatan - <?= htmlspecialchars($sp['nama_lngkp']) ?> (<?= htmlspecialchars($sp['nomor_sp']) ?>)</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
    
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm 15mm 20mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px 0;
            line-height: 1.45;
        }

        /* Non-print toolbar */
        .no-print-bar {
            width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-print {
            background-color: #0d6efd;
            color: #fff;
        }
        .btn-print:hover {
            background-color: #0b5ed7;
            color: #fff;
        }

        .btn-back {
            background-color: #6c757d;
            color: #fff;
        }
        .btn-back:hover {
            background-color: #5c636a;
            color: #fff;
        }

        .btn-download-file {
            background-color: #198754;
            color: #fff;
        }
        .btn-download-file:hover {
            background-color: #157347;
            color: #fff;
        }

        /* A4 Page Container */
        .paper-page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 20mm 22mm 15mm 22mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header / Kop */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .doc-header .logo-box {
            flex: 0 0 50%;
        }

        .doc-header .logo-box img {
            max-height: 60px;
            width: auto;
            display: block;
        }

        .doc-header .slogan-box {
            flex: 0 0 50%;
            text-align: right;
            color: #002060;
            font-weight: 900;
            font-size: 13.5pt;
            letter-spacing: 0.5px;
            padding-top: 15px;
        }

        /* Title */
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .doc-title h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .doc-title .doc-number {
            font-size: 11pt;
            margin-top: 3px;
        }

        /* Body Elements */
        .text-justify {
            text-align: justify;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 14px 0;
            line-height: 1.5;
        }

        .content-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .content-table td.col-label {
            width: 150px;
        }

        .content-table td.col-colon {
            width: 15px;
            text-align: center;
        }

        .content-table td.col-value {
            font-weight: bold;
        }

        .violation-list {
            margin: 8px 0 14px 0;
            padding-left: 24px;
        }

        .violation-list li {
            margin-bottom: 8px;
            line-height: 1.45;
            text-align: justify;
        }

        .clause-box {
            font-style: italic;
            margin: 10px 0 14px 0;
            text-align: justify;
            line-height: 1.45;
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Signatures */
        .signature-section {
            margin-top: 25px;
            margin-bottom: 20px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
        }

        .signature-space {
            height: 70px;
        }

        .signatory-name {
            font-weight: bold;
            text-decoration: underline;
            display: inline-block;
        }

        .signatory-title {
            display: block;
            margin-top: 2px;
        }

        /* Tembusan */
        .tembusan-section {
            border-top: 1px dotted #555;
            padding-top: 8px;
            margin-top: 15px;
            font-size: 9.5pt;
        }

        /* Footer */
        .doc-footer {
            margin-top: 25px;
            padding-top: 6px;
            font-size: 8.5pt;
            color: #333;
            line-height: 1.35;
        }

        .footer-address {
            color: #1a3b8b;
            font-weight: bold;
        }

        .footer-contact {
            color: #008080;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .paper-page {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            .doc-header .slogan-box {
                color: #002060 !important;
            }

            .footer-address {
                color: #1a3b8b !important;
            }

            .footer-contact {
                color: #008080 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Non-print action toolbar -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="javascript:window.close();" class="btn-action btn-back" onclick="if(window.history.length > 1){ window.history.back(); return false; }">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <span style="font-weight: bold; font-size: 14px; color: #333;">
                <i class="bi bi-file-earmark-check-fill text-danger me-1"></i>
                Dokumen Surat Peringatan: <?= htmlspecialchars($sp['nomor_sp']) ?>
            </span>
        </div>
        <div style="display: flex; gap: 8px;">
            <?php if (!empty($sp['file_sp'])) { ?>
                <a href="uploads/surat_peringatan/<?= htmlspecialchars($sp['file_sp']) ?>" target="_blank" class="btn-action btn-download-file">
                    <i class="bi bi-paperclip"></i> Lihat Berkas Lampiran
                </a>
            <?php } ?>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Official A4 Document Paper -->
    <div class="paper-page">
        <div>
            <!-- Kop Surat -->
            <div class="doc-header">
                <div class="logo-box">
                    <?php if ($logo_exists) { ?>
                        <img src="<?= $logo_path ?>" alt="PT KARISMA INDOAGRO UNIVERSAL">
                    <?php } else { ?>
                        <h2 style="margin: 0; color: #cc0000; font-weight: bold;">PT KARISMA INDOAGRO UNIVERSAL</h2>
                    <?php } ?>
                </div>
                <div class="slogan-box">
                    TRADISI, REPUTASI, INOVASI
                </div>
            </div>

            <!-- Judul Dokumen -->
            <div class="doc-title">
                <h1>SURAT PERINGATAN</h1>
                <div class="doc-number">Nomor : <?= htmlspecialchars($nomor_sp) ?></div>
            </div>

            <!-- Paragraf Pembuka -->
            <p class="text-justify" style="margin-bottom: 8px;">
                Sehubungan dengan pelanggaran yang dilakukan, dengan ini perusahaan memberikan Surat Peringatan kepada :
            </p>

            <!-- Data Karyawan -->
            <table class="content-table">
                <tr>
                    <td class="col-label">Nama</td>
                    <td class="col-colon">:</td>
                    <td class="col-value"><?= htmlspecialchars($sp['nama_lngkp']) ?></td>
                </tr>
                <tr>
                    <td class="col-label">Departemen</td>
                    <td class="col-colon">:</td>
                    <td><?= htmlspecialchars($sp['departement'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="col-label">Jabatan</td>
                    <td class="col-colon">:</td>
                    <td><?= htmlspecialchars($sp['jabatan'] ?? '-') ?></td>
                </tr>
            </table>

            <!-- Uraian Pelanggaran -->
            <p class="text-justify" style="margin-bottom: 6px;">
                Atas sikap indisipliner dan pelanggaran terhadap tata tertib perusahaan yang saudara lakukan yaitu melanggar :
            </p>

            <ol class="violation-list">
                <li>
                    <strong><?= htmlspecialchars($aturan_title) ?></strong>
                    <?php if (!empty($aturan_detail)) { ?>
                        <br>
                        <span><?= nl2br(htmlspecialchars($aturan_detail)) ?></span>
                    <?php } ?>
                </li>
                <li>
                    <strong>Uraian Pelanggaran 1 :</strong><br>
                    <span><?= nl2br(htmlspecialchars($raw_alasan)) ?></span>
                </li>
                <?php if (!empty($raw_alasan_2)) { ?>
                <li>
                    <strong>Uraian Pelanggaran 2 :</strong><br>
                    <span><?= nl2br(htmlspecialchars($raw_alasan_2)) ?></span>
                </li>
                <?php } ?>
                <li>
                    <strong>Tanggal kejadian :</strong><br>
                    <?= $tanggal_kejadian_indo ?>
                </li>
            </ol>

            <!-- Klausul Sanksi SP -->
            <p class="text-justify" style="margin-bottom: 6px;">
                Maka dengan ini perusahaan memberikan <strong><?= $sp_title ?></strong> dengan ketentuan sebagai berikut :
            </p>

            <div class="clause-box">
                “<?= $sp_ketentuan ?>”
            </div>

            <!-- Imbauan & Penutup -->
            <p class="text-justify" style="margin-bottom: 10px;">
                Surat Peringatan ini bertujuan untuk memberikan pengarahan sekaligus peringatan kepada saudara agar bersedia melaksanakan Peraturan Perusahaan, SOP, tata tertib perusahaan, dan tidak melakukan kesalahan yang dapat merugikan pihak perusahaan dan rekan sekerja.
            </p>

            <p class="text-justify" style="margin-bottom: 18px;">
                Demikian Surat Peringatan ini diberikan untuk dapat dijadikan sebagai bahan perhatian dan instropeksi diri.
            </p>

            <!-- Area Tanda Tangan -->
            <div class="signature-section">
                <div>Jember, <?= $tanggal_sp_indo ?></div>
                <div style="font-weight: bold; margin-bottom: 8px;">PT Karisma Indoagro Universal</div>
                
                <table class="signature-table">
                    <tr>
                        <td>
                            Dibuat,
                            <div class="signature-space"></div>
                            <span class="signatory-name"><?= htmlspecialchars($penandatangan) ?></span>
                            <span class="signatory-title"><?= htmlspecialchars($jabatan_penandatangan) ?></span>
                        </td>
                        <td>
                            Yang bersangkutan,
                            <div class="signature-space"></div>
                            <span class="signatory-name"><?= htmlspecialchars($sp['nama_lngkp']) ?></span>
                            <span class="signatory-title">Karyawan</span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Tembusan -->
            <div class="tembusan-section">
                <strong>Tembusan :</strong> <?= htmlspecialchars($tembusan) ?>
            </div>
        </div>

        <!-- Footer Alamat & Kontak Perusahaan -->
        <div class="doc-footer">
            <div class="footer-address">Jl. Semeru No.89 Ajung 68175, Jember - Jawa Timur</div>
            <div class="footer-contact">Phone. : +62 (331) 4833 33, 4832 11-4877 88 &nbsp;&nbsp;|&nbsp;&nbsp; e-mail : karisma.kiu@gmail.com</div>
        </div>
    </div>

</body>
</html>
