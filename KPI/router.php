<?php
// Central route map for legacy flat URLs.
$routes = [
    'index' => 'index.php',

    'adminhrd' => 'app/adminhrd/adminhrd.php',
    'archive-adminhrd' => 'app/adminhrd/archive-adminhrd.php',
    'archive-adminhrd-detail' => 'app/adminhrd/archive-adminhrd-detail.php',
    'dashboard-adminhrd' => 'app/adminhrd/dashboard-adminhrd.php',
    'datakpi-adminhrd' => 'app/adminhrd/datakpi-adminhrd.php',
    'datauser-adminhrd' => 'app/adminhrd/datauser-adminhrd.php',
    'eviden-adminhrd' => 'app/adminhrd/eviden-adminhrd.php',
    'eviden-adminhrd-detail' => 'app/adminhrd/eviden-adminhrd-detail.php',
    'kpi-lock-settings-adminhrd' => 'app/adminhrd/kpi-lock-settings-adminhrd.php',
    'skill-standard-adminhrd' => 'app/adminhrd/skill-standard-adminhrd.php',
    'penilaian-karakter-adminhrd' => 'app/adminhrd/penilaian-karakter-adminhrd.php',

    'home-adminedp' => 'app/adminedp/home-adminedp.php',

    'home-kpi-real' => 'app/dashboard/home-kpi-real.php',
    'dashboard-bu' => 'app/dashboard/dashboard-bu.php',
    'home-kpi-simulasi' => 'app/dashboard/home-kpi-simulasi.php',
    'dashboard-utama' => 'app/dashboard/dashboard-utama.php',
    'detail-kpi-real' => 'app/dashboard/detail-kpi-real.php',

    'detail-kpi-simulasi' => 'app/dashboard/detail-kpi-simulasi.php',
    'kpianggota' => 'app/kpi/kpianggota.php',
    'kpidepartemen' => 'app/kpi/kpidepartemen.php',
    'kpidetailanggota' => 'app/kpi/kpidetailanggota.php',
    'kpidirektur' => 'app/kpi/kpidirektur.php',
    'kpikabag' => 'app/kpi/kpikabag.php',
    'kpikadep' => 'app/kpi/kpikadep.php',

    'skillstandard' => 'app/skill-standard/skillstandard.php',
    'ssanggota' => 'app/skill-standard/ssanggota.php',
    'ssanggotadetail' => 'app/skill-standard/ssanggotadetail.php',
    'penilaian-karakter' => 'app/skill-standard/penilaian-karakter.php',

    'archive' => 'app/archive/archive.php',
    'archiveangdet' => 'app/archive/archiveangdet.php',
    'archiveanggota' => 'app/archive/archiveanggota.php',
    'archiveangpoin' => 'app/archive/archiveangpoin.php',
    'archivedetail' => 'app/archive/archivedetail.php',
    'archivekabag' => 'app/archive/archivekabag.php',
    'archivepoin' => 'app/archive/archivepoin.php',

    'eviden' => 'app/eviden/eviden.php',
    'evidenanggota' => 'app/eviden/evidenanggota.php',
    'evidenkabag' => 'app/eviden/evidenkabag.php',

    'export_kpi_all_adminhrd' => 'app/exports/export_kpi_all_adminhrd.php',
    'export_kpi_detail' => 'app/exports/export_kpi_detail.php',
    'export_kpisim_detail' => 'app/exports/export_kpisim_detail.php',

    'sop' => 'app/sop/sop.php',
    'sop-departemen' => 'app/sop/sop-departemen.php',
    'sop-prioritas' => 'app/sop/sop-prioritas.php',
    'updatesop' => 'app/sop/updatesop.php',

    'error' => 'app/system/error.php',
    'logout' => 'app/system/logout.php',
    'noaccess' => 'app/system/noaccess.php',
    'profile-settings' => 'app/system/profile-settings.php',
    'register' => 'app/system/register.php',
];

$page = isset($_GET['page']) ? trim($_GET['page'], '/') : '';

if (!isset($routes[$page])) {
    http_response_code(404);
    require __DIR__ . '/app/system/error.php';
    exit;
}

$target = __DIR__ . '/' . $routes[$page];

if (!is_file($target)) {
    http_response_code(404);
    require __DIR__ . '/app/system/error.php';
    exit;
}

chdir(__DIR__);
require $target;
