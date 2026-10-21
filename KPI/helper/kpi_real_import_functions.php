<?php

function canImportKPIRealForMember(
    $currentUserId,
    $targetUserId,
    $level,
    $currentUserName,
    $currentDepartment,
    $targetSupervisor,
    $targetDepartment
)
{
    if ((int) $targetUserId <= 0 || (int) $currentUserId === (int) $targetUserId) {
        return false;
    }

    $level = (int) $level;
    return (string) $targetSupervisor === stripslashes((string) $currentUserName)
        || ($level === 4 && (string) $targetDepartment === (string) $currentDepartment)
        || in_array($level, [5, 6, 7], true);
}

function kpiRealImportNumber($value)
{
    if (is_int($value) || is_float($value)) {
        return (float) $value;
    }

    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    $value = preg_replace('/[^0-9,\.\-]/', '', $value);
    if ($value === '' || $value === '-') {
        return null;
    }

    $lastComma = strrpos($value, ',');
    $lastDot = strrpos($value, '.');
    if ($lastComma !== false && $lastDot !== false) {
        if ($lastComma > $lastDot) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            $value = str_replace(',', '', $value);
        }
    } elseif ($lastComma !== false) {
        $value = str_replace(',', '.', $value);
    }

    return is_numeric($value) ? (float) $value : null;
}

function kpiRealNormalizeName($value)
{
    return strtoupper(preg_replace('/\s+/u', ' ', trim((string) $value)));
}

function kpiRealLegacyAssessmentType($value)
{
    $value = strtoupper(trim((string) $value));
    if (in_array($value, ['A', 'INDIKATOR'], true)) {
        return 'A';
    }
    if (in_array($value, ['B', 'ANGKA', 'TARGET', 'OMSET'], true)) {
        return 'B';
    }
    return null;
}

function importKPIRealResultsForMember($conn, $targetUserId, $editorUserId, $sheet, $dryRun = false, $spreadsheet = null)
{
    $firstHeader = strtoupper(trim((string) $sheet->getCell('A1')->getValue()));
    $secondHeader = strtoupper(trim((string) $sheet->getCell('B1')->getValue()));
    if ($firstHeader !== 'JENIS' || $secondHeader !== 'POIN KPI') {
        throw new RuntimeException('Header template tidak sesuai. Download kembali template KPI Real anggota.');
    }

    $targetUserId = (int) $targetUserId;
    $editorUserId = (int) $editorUserId;
    $updated = 0;
    $created = 0;
    $skipped = 0;
    $errors = [];

    // Dynamically map columns by header name
    $headerMap = [];
    $highestCol = $sheet->getHighestColumn();
    $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);
    for ($i = 1; $i <= $highestColIndex; $i++) {
        $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
        $headerVal = strtoupper(trim((string) $sheet->getCell($colStr . '1')->getValue()));
        if ($headerVal !== '') {
            $headerMap[$headerVal] = $colStr;
        }
    }

    $colKind = $headerMap['JENIS'] ?? 'A';
    $colKpiPoint = $headerMap['POIN KPI'] ?? 'B';
    $colPointWeight = $headerMap['BOBOT POIN (%)'] ?? null;
    $colIndicatorPoint = $headerMap['INDIKATOR KPI'] ?? ($headerMap['DETAIL KPI'] ?? ($colPointWeight ? 'D' : 'C'));
    $colType = $headerMap['TIPE'] ?? ($colPointWeight ? 'E' : 'D');
    $colWeight = $headerMap['BOBOT (%)'] ?? ($headerMap['BOBOT'] ?? ($colPointWeight ? 'F' : 'E'));
    $colTarget = $headerMap['TARGET'] ?? ($headerMap['TARGET (HANYA TIPE B)'] ?? ($colPointWeight ? 'G' : 'F'));
    $colResult = $headerMap['NILAI (TIPE A)'] ?? ($headerMap['HASIL REALISASI'] ?? ($headerMap['NILAI'] ?? ($colPointWeight ? 'H' : 'G')));
    $colScore = $headerMap['NILAI (TIPE A)'] ?? ($colPointWeight ? 'H' : 'G');

    // Load PILIHAN NILAI if sheet exists
    $choicesByIndicator = [];
    if ($spreadsheet) {
        $choiceSheet = $spreadsheet->getSheetByName('PILIHAN NILAI');
        if ($choiceSheet) {
            for ($r = 2; $r <= $choiceSheet->getHighestDataRow(); $r++) {
                $cKind = strtoupper(trim((string) $choiceSheet->getCell('A' . $r)->getValue()));
                $cKpiPoint = trim((string) $choiceSheet->getCell('B' . $r)->getValue());
                $cIndicator = trim((string) $choiceSheet->getCell('C' . $r)->getValue());
                $cDesc = trim((string) $choiceSheet->getCell('D' . $r)->getValue());
                $cVal = kpiRealImportNumber($choiceSheet->getCell('E' . $r)->getValue());
                if ($cDesc === '' && $cVal === null) continue;
                $key = $cKind . '|' . $cKpiPoint . '|' . $cIndicator;
                $choicesByIndicator[$key][] = ['desc' => $cDesc, 'val' => $cVal];
            }
        }
    }

    // Pre-scan distinct points from sheet
    $whatPoints = [];
    $howPoints = [];
    $pointWeights = [];
    for ($r = 2; $r <= $sheet->getHighestDataRow(); $r++) {
        $k = strtoupper(trim((string) $sheet->getCell($colKind . $r)->getValue()));
        $p = trim((string) $sheet->getCell($colKpiPoint . $r)->getValue());
        $pw = $colPointWeight ? kpiRealImportNumber($sheet->getCell($colPointWeight . $r)->getValue()) : null;
        if ($k === 'WHAT' && $p !== '' && !in_array($p, $whatPoints, true)) {
            $whatPoints[] = $p;
            if ($pw !== null) $pointWeights['WHAT|' . $p] = $pw;
        }
        if ($k === 'HOW' && $p !== '' && !in_array($p, $howPoints, true)) {
            $howPoints[] = $p;
            if ($pw !== null) $pointWeights['HOW|' . $p] = $pw;
        }
    }

    mysqli_begin_transaction($conn);
    try {
        // If target user has 0 tb_kpi rows, initialize groups
        $checkKpi = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM tb_kpi WHERE id_user=$targetUserId");
        $kpiCount = ($checkKpi && ($r = mysqli_fetch_assoc($checkKpi))) ? (int) $r['cnt'] : 0;
        if ($kpiCount === 0 && (!empty($whatPoints) || !empty($howPoints))) {
            $maxGroups = max(count($whatPoints), count($howPoints));
            for ($i = 0; $i < $maxGroups; $i++) {
                $pw = $whatPoints[$i] ?? '';
                $ph = $howPoints[$i] ?? '';
                $pwSafe = mysqli_real_escape_string($conn, $pw);
                $phSafe = mysqli_real_escape_string($conn, $ph);

                $weightW = $pointWeights['WHAT|' . $pw] ?? null;
                if ($weightW === null) {
                    if (strcasecmp($pw, 'Absensi') === 0) {
                        $weightW = 10;
                    } elseif ($maxGroups === 4) {
                        $weightW = ($i === 3) ? 10 : 30;
                    } else {
                        $weightW = round(100 / $maxGroups, 2);
                    }
                }
                $weightH = $pointWeights['HOW|' . $ph] ?? null;
                if ($weightH === null) {
                    if (stripos($ph, 'Absensi') === 0) {
                        $weightH = 10;
                    } elseif ($maxGroups === 4) {
                        $weightH = ($i === 3) ? 10 : 30;
                    } else {
                        $weightH = round(100 / $maxGroups, 2);
                    }
                }
                $weightWSql = number_format($weightW, 2, '.', '');
                $weightHSql = number_format($weightH, 2, '.', '');
                mysqli_query($conn, "INSERT INTO tb_kpi (id_user, poin, bobot, poin2, bobot2) VALUES ($targetUserId, '$pwSafe', $weightWSql, '$phSafe', $weightHSql)");
            }
        }

        for ($row = 2; $row <= $sheet->getHighestDataRow(); $row++) {
            $kind = strtoupper(trim((string) $sheet->getCell($colKind . $row)->getValue()));
            $kpiPoint = trim((string) $sheet->getCell($colKpiPoint . $row)->getValue());
            $indicatorPoint = trim((string) $sheet->getCell($colIndicatorPoint . $row)->getValue());
            $targetInput = $sheet->getCell($colTarget . $row)->getValue();
            $resultInput = $sheet->getCell($colResult . $row)->getValue();
            $scoreInput = $sheet->getCell($colScore . $row)->getValue();
            $pointWeightInput = $colPointWeight ? $sheet->getCell($colPointWeight . $row)->getValue() : null;

            if ($kind === '' && $kpiPoint === '' && $indicatorPoint === '') {
                continue;
            }
            if (!in_array($kind, ['WHAT', 'HOW'], true) || $kpiPoint === '' || $indicatorPoint === '') {
                $skipped++;
                $errors[] = "Baris $row: Jenis, Poin KPI, dan Indikator KPI wajib diisi.";
                continue;
            }

            $table = $kind === 'WHAT' ? 'tb_whats' : 'tb_hows';
            $idColumn = $kind === 'WHAT' ? 'id_what' : 'id_how';
            $typeColumn = $kind === 'WHAT' ? 'tipe_what' : 'tipe_how';
            $pointColumn = $kind === 'WHAT' ? 'p_what' : 'p_how';
            $kpiPointColumn = $kind === 'WHAT' ? 'poin' : 'poin2';
            $indicatorTable = $kind === 'WHAT' ? 'tb_indikator_whats' : 'tb_indikator_hows';
            $indicatorForeignKey = $kind === 'WHAT' ? 'id_what' : 'id_how';
            $kpiPointSafe = mysqli_real_escape_string($conn, $kpiPoint);
            $indicatorPointSafe = mysqli_real_escape_string($conn, $indicatorPoint);

            if ($pointWeightInput !== null) {
                $pWeight = kpiRealImportNumber($pointWeightInput);
                if ($pWeight !== null && $pWeight >= 0 && $pWeight <= 100) {
                    $pWeightSql = number_format($pWeight, 2, '.', '');
                    $weightField = $kind === 'WHAT' ? 'bobot' : 'bobot2';
                    mysqli_query($conn, "UPDATE tb_kpi SET `$weightField`=$pWeightSql WHERE id_user=$targetUserId AND `$kpiPointColumn`='$kpiPointSafe'");
                }
            }

            // Find group in tb_kpi
            $groupResult = mysqli_query($conn, "SELECT id FROM tb_kpi WHERE id_user=$targetUserId AND TRIM(`$kpiPointColumn`)='$kpiPointSafe' LIMIT 1");
            $group = $groupResult ? mysqli_fetch_assoc($groupResult) : null;
            if (!$group) {
                $weightField = $kind === 'WHAT' ? 'bobot' : 'bobot2';
                mysqli_query($conn, "INSERT INTO tb_kpi (id_user, `$kpiPointColumn`, `$weightField`) VALUES ($targetUserId, '$kpiPointSafe', 0)");
                $groupId = (int) mysqli_insert_id($conn);
            } else {
                $groupId = (int) $group['id'];
            }

            $itemResult = mysqli_query(
                $conn,
                "SELECT item.`$idColumn` AS id_item, item.`$typeColumn` AS tipe, item.bobot, item.target_omset "
                . "FROM `$table` item "
                . "WHERE item.id_user=$targetUserId AND item.id_kpi=$groupId "
                . "AND TRIM(item.`$pointColumn`)='$indicatorPointSafe' LIMIT 2"
            );
            $item = $itemResult ? mysqli_fetch_assoc($itemResult) : null;
            $isNewItem = false;

            if (!$item) {
                // Auto-create missing item
                $itemType = strtoupper(trim((string) $sheet->getCell($colType . $row)->getValue()));
                if (!in_array($itemType, ['A', 'B'], true)) $itemType = 'A';
                $itemWeight = kpiRealImportNumber($sheet->getCell($colWeight . $row)->getValue()) ?? 0;
                $itemTarget = kpiRealImportNumber($targetInput) ?? 0;
                $weightSql = number_format($itemWeight, 2, '.', '');
                $targetSql = number_format($itemTarget, 2, '.', '');

                $insertOk = mysqli_query(
                    $conn,
                    "INSERT INTO `$table` (id_user, id_kpi, `$typeColumn`, `$pointColumn`, bobot, target_omset, hasil, nilai, total, is_edited, edited_by, edited_at) "
                    . "VALUES ($targetUserId, $groupId, '$itemType', '$indicatorPointSafe', $weightSql, $targetSql, '', 0, 0, 1, $editorUserId, NOW())"
                );
                if (!$insertOk) {
                    $skipped++;
                    $errors[] = "Baris $row: Gagal membuat item detail KPI.";
                    continue;
                }
                $itemId = (int) mysqli_insert_id($conn);
                $item = [
                    'id_item' => $itemId,
                    'tipe' => $itemType,
                    'bobot' => $itemWeight,
                    'target_omset' => $itemTarget,
                ];
                $isNewItem = true;
                $created++;

                // Auto-populate choices from PILIHAN NILAI if available
                $choiceKey = $kind . '|' . $kpiPoint . '|' . $indicatorPoint;
                if (isset($choicesByIndicator[$choiceKey])) {
                    $order = 1;
                    foreach ($choicesByIndicator[$choiceKey] as $choice) {
                        $descSafe = mysqli_real_escape_string($conn, $choice['desc']);
                        $valSql = number_format($choice['val'] ?? 0, 2, '.', '');
                        mysqli_query($conn, "INSERT INTO `$indicatorTable` (`$indicatorForeignKey`, keterangan, nilai, urutan, is_edited, edited_by, edited_at) VALUES ($itemId, '$descSafe', $valSql, $order, 1, $editorUserId, NOW())");
                        $order++;
                    }
                }
            } elseif (mysqli_num_rows($itemResult) > 1) {
                $skipped++;
                $errors[] = "Baris $row: Poin/Indikator KPI namanya ganda.";
                continue;
            }

            $itemId = (int) $item['id_item'];
            $type = strtoupper((string) $item['tipe']);
            $weight = (float) $item['bobot'];

            $score = kpiRealImportNumber($scoreInput);
            $resultText = trim((string) $resultInput);
            if ($score === null && $resultText === '') {
                continue;
            }

            if ($type === 'A') {
                $indicator = null;
                if ($resultText !== '') {
                    $resultTextSafe = mysqli_real_escape_string($conn, $resultText);
                    $indicatorResult = mysqli_query(
                        $conn,
                        "SELECT keterangan, nilai FROM `$indicatorTable` WHERE `$indicatorForeignKey`=$itemId "
                        . "AND TRIM(keterangan)='$resultTextSafe' LIMIT 1"
                    );
                    if ($indicatorResult && mysqli_num_rows($indicatorResult) > 0) {
                        $indicator = mysqli_fetch_assoc($indicatorResult);
                    }
                }

                if (!$indicator) {
                    $numericScore = $score !== null ? $score : kpiRealImportNumber($resultInput);
                    if ($numericScore !== null) {
                        $scoreSql = number_format($numericScore, 2, '.', '');
                        $indicatorResult = mysqli_query(
                            $conn,
                            "SELECT keterangan, nilai FROM `$indicatorTable` "
                            . "WHERE `$indicatorForeignKey`=$itemId AND nilai=$scoreSql LIMIT 1"
                        );
                        if ($indicatorResult && mysqli_num_rows($indicatorResult) > 0) {
                            $indicator = mysqli_fetch_assoc($indicatorResult);
                        }
                    }
                }

                if (!$indicator) {
                    $skipped++;
                    $errors[] = "Baris $row: nilai/hasil tidak tersedia pada sheet PILIHAN NILAI.";
                    continue;
                }
                $score = (float) $indicator['nilai'];
                $resultTextSafe = mysqli_real_escape_string($conn, (string) $indicator['keterangan']);
                $total = round($score * $weight / 100, 2);
                $sql = "UPDATE `$table` SET original_hasil=COALESCE(original_hasil, hasil), "
                    . "original_nilai=COALESCE(original_nilai, nilai), original_total=COALESCE(original_total, total), "
                    . "hasil='$resultTextSafe', nilai=$score, total=$total, is_edited=1, "
                    . "edited_by=$editorUserId, edited_at=NOW() WHERE `$idColumn`=$itemId AND id_user=$targetUserId";
            } elseif ($type === 'B') {
                $resultValue = kpiRealImportNumber($resultInput);
                if ($resultValue === null) {
                    continue;
                }
                $targetValue = kpiRealImportNumber($targetInput);
                if ($targetValue === null) {
                    $targetValue = (float) $item['target_omset'];
                }
                if ($targetValue <= 0) {
                    $skipped++;
                    $errors[] = "Baris $row: target tipe B harus lebih dari 0.";
                    continue;
                }
                $score = round(($resultValue / $targetValue) * 100, 2);
                $total = round($score * $weight / 100, 2);
                $resultTextSafe = mysqli_real_escape_string($conn, ' Hasil Tercapai: ' . number_format($resultValue, 2));
                $targetSql = number_format($targetValue, 2, '.', '');
                $sql = "UPDATE `$table` SET original_hasil=COALESCE(original_hasil, hasil), "
                    . "original_nilai=COALESCE(original_nilai, nilai), original_total=COALESCE(original_total, total), "
                    . "original_target_omset=COALESCE(original_target_omset, target_omset), target_omset=$targetSql, "
                    . "hasil='$resultTextSafe', nilai=$score, total=$total, is_edited=1, "
                    . "edited_by=$editorUserId, edited_at=NOW() WHERE `$idColumn`=$itemId AND id_user=$targetUserId";
            } else {
                $skipped++;
                $errors[] = "Baris $row: tipe penilaian tidak didukung.";
                continue;
            }

            if (mysqli_query($conn, $sql)) {
                $updated++;
            } else {
                throw new RuntimeException("Baris $row gagal memperbarui KPI Real.");
            }
        }

        if ($dryRun) {
            mysqli_rollback($conn);
        } else {
            mysqli_commit($conn);
        }
    } catch (Throwable $exception) {
        mysqli_rollback($conn);
        throw $exception;
    }

    return [
        'mode' => 'results',
        'updated' => $updated + $created,
        'created' => $created,
        'groups' => 0,
        'details' => 0,
        'indicators' => 0,
        'skipped' => $skipped,
        'errors' => $errors,
    ];
}

function importLegacyKPIRealForMember($conn, $targetUserId, $editorUserId, $spreadsheet, $dryRun = false)
{
    $pointSheet = $spreadsheet->getSheetByName('poin what');
    $goalSheet = $spreadsheet->getSheetByName('goals what');
    $indicatorSheet = $spreadsheet->getSheetByName('indikator what');
    if (!$pointSheet || !$goalSheet || !$indicatorSheet) {
        throw new RuntimeException('Format file tidak dikenali. Gunakan template terbaru atau format KPI lama yang lengkap.');
    }

    $targetUserId = (int) $targetUserId;
    $editorUserId = (int) $editorUserId;
    $userResult = mysqli_query($conn, "SELECT nama_lngkp FROM tb_users WHERE id=$targetUserId LIMIT 1");
    $targetUser = $userResult ? mysqli_fetch_assoc($userResult) : null;
    $fileEmployeeName = trim((string) $pointSheet->getCell('A2')->getValue());
    if ($fileEmployeeName === '') {
        $fileEmployeeName = trim((string) $goalSheet->getCell('A2')->getValue());
    }
    if (!$targetUser || $fileEmployeeName === ''
        || kpiRealNormalizeName($fileEmployeeName) !== kpiRealNormalizeName($targetUser['nama_lngkp'])) {
        throw new RuntimeException('Nama karyawan pada file tidak sama dengan anggota yang sedang dibuka.');
    }

    $groupsAdded = 0;
    $detailsAdded = 0;
    $indicatorsAdded = 0;
    $skipped = 0;
    $errors = [];
    $whatGroups = [];
    $howGroups = [];
    $whatItems = [];
    $howItems = [];

    mysqli_begin_transaction($conn);
    try {
        for ($row = 2; $row <= $pointSheet->getHighestDataRow(); $row++) {
            $pointWhat = trim((string) $pointSheet->getCell('B' . $row)->getValue());
            $weightWhat = kpiRealImportNumber($pointSheet->getCell('C' . $row)->getValue());
            $pointHow = trim((string) $pointSheet->getCell('D' . $row)->getValue());
            $weightHow = kpiRealImportNumber($pointSheet->getCell('E' . $row)->getValue());
            if ($pointWhat === '' && $pointHow === '') {
                continue;
            }
            if ($pointWhat === '' || $pointHow === '' || $weightWhat === null || $weightHow === null) {
                $skipped++;
                $errors[] = "Baris poin what $row tidak lengkap.";
                continue;
            }

            $pointWhatSafe = mysqli_real_escape_string($conn, $pointWhat);
            $pointHowSafe = mysqli_real_escape_string($conn, $pointHow);
            $groupResult = mysqli_query(
                $conn,
                "SELECT id FROM tb_kpi WHERE id_user=$targetUserId "
                . "AND TRIM(poin)='$pointWhatSafe' AND TRIM(poin2)='$pointHowSafe' LIMIT 1"
            );
            $group = $groupResult ? mysqli_fetch_assoc($groupResult) : null;
            if (!$group) {
                $compatibleGroupResult = mysqli_query(
                    $conn,
                    "SELECT id FROM tb_kpi WHERE id_user=$targetUserId "
                    . "AND (TRIM(poin)='$pointWhatSafe' OR TRIM(poin2)='$pointHowSafe') LIMIT 2"
                );
                if ($compatibleGroupResult && mysqli_num_rows($compatibleGroupResult) === 1) {
                    $group = mysqli_fetch_assoc($compatibleGroupResult);
                }
            }
            if ($group) {
                $groupId = (int) $group['id'];
            } else {
                $weightWhatSql = number_format($weightWhat, 2, '.', '');
                $weightHowSql = number_format($weightHow, 2, '.', '');
                $insertGroup = mysqli_query(
                    $conn,
                    "INSERT INTO tb_kpi (id_user, poin, bobot, poin2, bobot2) "
                    . "VALUES ($targetUserId, '$pointWhatSafe', $weightWhatSql, '$pointHowSafe', $weightHowSql)"
                );
                if (!$insertGroup) {
                    throw new RuntimeException("Baris poin what $row gagal membuat Poin KPI.");
                }
                $groupId = (int) mysqli_insert_id($conn);
                $groupsAdded++;
            }
            $whatGroups[$pointWhat] = $groupId;
            $howGroups[$pointHow] = $groupId;
        }

        $lastWhatPoint = '';
        $lastHowPoint = '';
        for ($row = 2; $row <= $goalSheet->getHighestDataRow(); $row++) {
            $whatPointCell = trim((string) $goalSheet->getCell('B' . $row)->getValue());
            $howPointCell = trim((string) $goalSheet->getCell('H' . $row)->getValue());
            if ($whatPointCell !== '') {
                $lastWhatPoint = $whatPointCell;
            }
            if ($howPointCell !== '') {
                $lastHowPoint = $howPointCell;
            }

            $sides = [
                [
                    'kind' => 'WHAT', 'point' => $lastWhatPoint,
                    'detail' => trim((string) $goalSheet->getCell('C' . $row)->getValue()),
                    'weight' => kpiRealImportNumber($goalSheet->getCell('D' . $row)->getValue()),
                    'type' => kpiRealLegacyAssessmentType($goalSheet->getCell('E' . $row)->getValue()),
                    'target' => kpiRealImportNumber($goalSheet->getCell('F' . $row)->getValue()),
                ],
                [
                    'kind' => 'HOW', 'point' => $lastHowPoint,
                    'detail' => trim((string) $goalSheet->getCell('I' . $row)->getValue()),
                    'weight' => kpiRealImportNumber($goalSheet->getCell('J' . $row)->getValue()),
                    'type' => kpiRealLegacyAssessmentType($goalSheet->getCell('K' . $row)->getValue()),
                    'target' => kpiRealImportNumber($goalSheet->getCell('L' . $row)->getValue()),
                ],
            ];

            foreach ($sides as $side) {
                if ($side['detail'] === '') {
                    continue;
                }
                $groupMap = $side['kind'] === 'WHAT' ? $whatGroups : $howGroups;
                if ($side['point'] === '' || !isset($groupMap[$side['point']])) {
                    $skipped++;
                    $errors[] = "Baris goals what $row: Poin {$side['kind']} tidak ditemukan di sheet poin what.";
                    continue;
                }
                if ($side['weight'] === null || $side['weight'] < 0 || $side['weight'] > 100 || $side['type'] === null) {
                    $skipped++;
                    $errors[] = "Baris goals what $row: bobot/tipe {$side['kind']} tidak valid.";
                    continue;
                }
                if ($side['type'] === 'B' && ($side['target'] === null || $side['target'] <= 0)) {
                    $skipped++;
                    $errors[] = "Baris goals what $row: jumlah angka tipe B wajib lebih dari 0.";
                    continue;
                }

                $groupId = (int) $groupMap[$side['point']];
                $table = $side['kind'] === 'WHAT' ? 'tb_whats' : 'tb_hows';
                $idColumn = $side['kind'] === 'WHAT' ? 'id_what' : 'id_how';
                $typeColumn = $side['kind'] === 'WHAT' ? 'tipe_what' : 'tipe_how';
                $pointColumn = $side['kind'] === 'WHAT' ? 'p_what' : 'p_how';
                $detailSafe = mysqli_real_escape_string($conn, $side['detail']);
                $duplicate = mysqli_query(
                    $conn,
                    "SELECT `$idColumn` FROM `$table` WHERE id_user=$targetUserId "
                    . "AND id_kpi=$groupId AND TRIM(`$pointColumn`)='$detailSafe' LIMIT 1"
                );
                if ($duplicate && mysqli_num_rows($duplicate) > 0) {
                    $duplicateRow = mysqli_fetch_assoc($duplicate);
                    if ($side['kind'] === 'WHAT') {
                        $whatItems[$side['detail']] = (int) $duplicateRow[$idColumn];
                    } else {
                        $howItems[$side['detail']] = (int) $duplicateRow[$idColumn];
                    }
                    $skipped++;
                    continue;
                }

                $weightSql = number_format($side['weight'], 2, '.', '');
                $targetSql = $side['type'] === 'B' ? number_format($side['target'], 2, '.', '') : '0';
                $insertDetail = mysqli_query(
                    $conn,
                    "INSERT INTO `$table` "
                    . "(id_user, id_kpi, `$typeColumn`, `$pointColumn`, bobot, target_omset, hasil, nilai, total, is_edited, edited_by, edited_at) "
                    . "VALUES ($targetUserId, $groupId, '{$side['type']}', '$detailSafe', $weightSql, $targetSql, '', 0, 0, 1, $editorUserId, NOW())"
                );
                if (!$insertDetail) {
                    throw new RuntimeException("Baris goals what $row gagal menyimpan Detail {$side['kind']}.");
                }
                if ($side['kind'] === 'WHAT') {
                    $whatItems[$side['detail']] = (int) mysqli_insert_id($conn);
                } else {
                    $howItems[$side['detail']] = (int) mysqli_insert_id($conn);
                }
                $detailsAdded++;
            }
        }

        $lastWhatDetail = '';
        $lastHowDetail = '';
        for ($row = 2; $row <= $indicatorSheet->getHighestDataRow(); $row++) {
            $whatDetailCell = trim((string) $indicatorSheet->getCell('A' . $row)->getValue());
            $howDetailCell = trim((string) $indicatorSheet->getCell('E' . $row)->getValue());
            if ($whatDetailCell !== '') {
                $lastWhatDetail = $whatDetailCell;
            }
            if ($howDetailCell !== '') {
                $lastHowDetail = $howDetailCell;
            }

            $sides = [
                ['kind' => 'WHAT', 'detail' => $lastWhatDetail, 'description' => trim((string) $indicatorSheet->getCell('B' . $row)->getValue()), 'score' => kpiRealImportNumber($indicatorSheet->getCell('C' . $row)->getValue())],
                ['kind' => 'HOW', 'detail' => $lastHowDetail, 'description' => trim((string) $indicatorSheet->getCell('F' . $row)->getValue()), 'score' => kpiRealImportNumber($indicatorSheet->getCell('G' . $row)->getValue())],
            ];

            foreach ($sides as $side) {
                if ($side['description'] === '') {
                    continue;
                }
                if ($side['detail'] === '' || $side['score'] === null) {
                    $skipped++;
                    $errors[] = "Baris indikator what $row: indikator {$side['kind']} tidak lengkap.";
                    continue;
                }

                $indicatorTable = $side['kind'] === 'WHAT' ? 'tb_indikator_whats' : 'tb_indikator_hows';
                $indicatorForeignKey = $side['kind'] === 'WHAT' ? 'id_what' : 'id_how';
                $itemMap = $side['kind'] === 'WHAT' ? $whatItems : $howItems;
                if (!isset($itemMap[$side['detail']])) {
                    $skipped++;
                    $errors[] = "Baris indikator what $row: Detail {$side['kind']} tidak ditemukan pada sheet goals what.";
                    continue;
                }

                $itemId = (int) $itemMap[$side['detail']];
                $descriptionSafe = mysqli_real_escape_string($conn, $side['description']);
                $duplicate = mysqli_query(
                    $conn,
                    "SELECT id_indikator FROM `$indicatorTable` WHERE `$indicatorForeignKey`=$itemId "
                    . "AND TRIM(keterangan)='$descriptionSafe' LIMIT 1"
                );
                if ($duplicate && mysqli_num_rows($duplicate) > 0) {
                    $skipped++;
                    continue;
                }
                $orderResult = mysqli_query($conn, "SELECT COALESCE(MAX(urutan), 0) + 1 AS next_order FROM `$indicatorTable` WHERE `$indicatorForeignKey`=$itemId");
                $orderRow = $orderResult ? mysqli_fetch_assoc($orderResult) : ['next_order' => 1];
                $order = max(1, (int) $orderRow['next_order']);
                $scoreSql = number_format($side['score'], 2, '.', '');
                $insertIndicator = mysqli_query(
                    $conn,
                    "INSERT INTO `$indicatorTable` (`$indicatorForeignKey`, keterangan, nilai, urutan, is_edited, edited_by, edited_at) "
                    . "VALUES ($itemId, '$descriptionSafe', $scoreSql, $order, 1, $editorUserId, NOW())"
                );
                if (!$insertIndicator) {
                    throw new RuntimeException("Baris indikator what $row gagal disimpan.");
                }
                $indicatorsAdded++;
            }
        }

        if ($dryRun) {
            mysqli_rollback($conn);
        } else {
            mysqli_commit($conn);
        }
    } catch (Throwable $exception) {
        mysqli_rollback($conn);
        throw $exception;
    }

    return [
        'updated' => $groupsAdded + $detailsAdded + $indicatorsAdded,
        'groups' => $groupsAdded,
        'details' => $detailsAdded,
        'indicators' => $indicatorsAdded,
        'skipped' => $skipped,
        'errors' => $errors,
    ];
}

function importKPIRealForMember($conn, $targetUserId, $editorUserId, $filePath, $dryRun = false)
{
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $resultSheet = $spreadsheet->getSheetByName('DATA KPI REAL');
    if ($resultSheet) {
        return importKPIRealResultsForMember($conn, $targetUserId, $editorUserId, $resultSheet, $dryRun, $spreadsheet);
    }
    $dataSheet = $spreadsheet->getSheetByName('DATA KPI');
    $indicatorSheet = $spreadsheet->getSheetByName('INDIKATOR TIPE A');
    if (!$dataSheet) {
        return importLegacyKPIRealForMember($conn, $targetUserId, $editorUserId, $spreadsheet, $dryRun);
    }

    $firstHeader = strtoupper(trim((string) $dataSheet->getCell('A1')->getValue()));
    $secondHeader = strtoupper(trim((string) $dataSheet->getCell('B1')->getValue()));
    if ($firstHeader !== 'POIN KPI WHAT' || $secondHeader !== 'BOBOT POIN WHAT (%)') {
        throw new RuntimeException('Header template tidak sesuai. Gunakan template KPI Real terbaru.');
    }

    $targetUserId = (int) $targetUserId;
    $editorUserId = (int) $editorUserId;
    $groupsAdded = 0;
    $detailsAdded = 0;
    $indicatorsAdded = 0;
    $skipped = 0;
    $errors = [];
    $groupMap = [];

    mysqli_begin_transaction($conn);
    try {
        $highestRow = $dataSheet->getHighestDataRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $pointWhat = trim((string) $dataSheet->getCell('A' . $row)->getValue());
            $weightWhat = kpiRealImportNumber($dataSheet->getCell('B' . $row)->getValue());
            $pointHow = trim((string) $dataSheet->getCell('C' . $row)->getValue());
            $weightHow = kpiRealImportNumber($dataSheet->getCell('D' . $row)->getValue());
            $kind = strtoupper(trim((string) $dataSheet->getCell('E' . $row)->getValue()));
            $detail = trim((string) $dataSheet->getCell('F' . $row)->getValue());
            $detailWeight = kpiRealImportNumber($dataSheet->getCell('G' . $row)->getValue());
            $assessmentType = strtoupper(trim((string) $dataSheet->getCell('H' . $row)->getValue()));
            $target = kpiRealImportNumber($dataSheet->getCell('I' . $row)->getValue());

            if ($pointWhat === '' && $pointHow === '' && $detail === '') {
                continue;
            }
            if (strpos(strtoupper($pointWhat), 'CONTOH:') === 0 || strpos(strtoupper($pointHow), 'CONTOH:') === 0) {
                continue;
            }
            if ($pointWhat === '' || $pointHow === '' || $detail === '') {
                $skipped++;
                $errors[] = "Baris DATA KPI $row: Poin KPI WHAT, Poin KPI HOW, dan Detail KPI wajib diisi.";
                continue;
            }
            if ($weightWhat === null || $weightHow === null || $detailWeight === null
                || $weightWhat < 0 || $weightWhat > 100
                || $weightHow < 0 || $weightHow > 100
                || $detailWeight < 0 || $detailWeight > 100) {
                $skipped++;
                $errors[] = "Baris DATA KPI $row: semua bobot harus berupa angka 0 sampai 100.";
                continue;
            }
            if (!in_array($kind, ['WHAT', 'HOW'], true) || !in_array($assessmentType, ['A', 'B'], true)) {
                $skipped++;
                $errors[] = "Baris DATA KPI $row: Jenis Detail harus WHAT/HOW dan Tipe Penilaian harus A/B.";
                continue;
            }
            if ($assessmentType === 'B' && ($target === null || $target <= 0)) {
                $skipped++;
                $errors[] = "Baris DATA KPI $row: Target wajib diisi lebih dari 0 untuk tipe B.";
                continue;
            }

            $pointWhatSafe = mysqli_real_escape_string($conn, $pointWhat);
            $pointHowSafe = mysqli_real_escape_string($conn, $pointHow);
            $groupKey = sha1($pointWhat . "\0" . $pointHow);
            if (!isset($groupMap[$groupKey])) {
                $groupResult = mysqli_query(
                    $conn,
                    "SELECT id FROM tb_kpi WHERE id_user=$targetUserId "
                    . "AND poin='$pointWhatSafe' AND poin2='$pointHowSafe' LIMIT 1"
                );
                $group = $groupResult ? mysqli_fetch_assoc($groupResult) : null;
                if ($group) {
                    $groupMap[$groupKey] = (int) $group['id'];
                } else {
                    $weightWhatSql = number_format($weightWhat, 2, '.', '');
                    $weightHowSql = number_format($weightHow, 2, '.', '');
                    $insertGroup = mysqli_query(
                        $conn,
                        "INSERT INTO tb_kpi (id_user, poin, bobot, poin2, bobot2) "
                        . "VALUES ($targetUserId, '$pointWhatSafe', $weightWhatSql, '$pointHowSafe', $weightHowSql)"
                    );
                    if (!$insertGroup) {
                        throw new RuntimeException("Baris DATA KPI $row gagal membuat Poin KPI.");
                    }
                    $groupMap[$groupKey] = (int) mysqli_insert_id($conn);
                    $groupsAdded++;
                }
            }

            $groupId = $groupMap[$groupKey];
            $table = $kind === 'WHAT' ? 'tb_whats' : 'tb_hows';
            $idColumn = $kind === 'WHAT' ? 'id_what' : 'id_how';
            $typeColumn = $kind === 'WHAT' ? 'tipe_what' : 'tipe_how';
            $pointColumn = $kind === 'WHAT' ? 'p_what' : 'p_how';
            $detailSafe = mysqli_real_escape_string($conn, $detail);
            $duplicateResult = mysqli_query(
                $conn,
                "SELECT `$idColumn` FROM `$table` WHERE id_user=$targetUserId "
                . "AND id_kpi=$groupId AND `$pointColumn`='$detailSafe' LIMIT 1"
            );
            if ($duplicateResult && mysqli_num_rows($duplicateResult) > 0) {
                $skipped++;
                continue;
            }

            $detailWeightSql = number_format($detailWeight, 2, '.', '');
            $targetSql = $assessmentType === 'B' ? number_format($target, 2, '.', '') : '0';
            $insertDetail = mysqli_query(
                $conn,
                "INSERT INTO `$table` "
                . "(id_user, id_kpi, `$typeColumn`, `$pointColumn`, bobot, target_omset, hasil, nilai, total, is_edited, edited_by, edited_at) "
                . "VALUES ($targetUserId, $groupId, '$assessmentType', '$detailSafe', $detailWeightSql, $targetSql, '', 0, 0, 1, $editorUserId, NOW())"
            );
            if (!$insertDetail) {
                throw new RuntimeException("Baris DATA KPI $row gagal menyimpan Detail KPI.");
            }
            $detailsAdded++;
        }

        if ($indicatorSheet) {
            $indicatorHeader = strtoupper(trim((string) $indicatorSheet->getCell('A1')->getValue()));
            if ($indicatorHeader !== 'POIN KPI') {
                throw new RuntimeException('Header sheet INDIKATOR TIPE A tidak sesuai template.');
            }

            $highestIndicatorRow = $indicatorSheet->getHighestDataRow();
            for ($row = 2; $row <= $highestIndicatorRow; $row++) {
                $pointName = trim((string) $indicatorSheet->getCell('A' . $row)->getValue());
                $kind = strtoupper(trim((string) $indicatorSheet->getCell('B' . $row)->getValue()));
                $detail = trim((string) $indicatorSheet->getCell('C' . $row)->getValue());
                $description = trim((string) $indicatorSheet->getCell('D' . $row)->getValue());
                $score = kpiRealImportNumber($indicatorSheet->getCell('E' . $row)->getValue());

                if ($pointName === '' && $detail === '' && $description === '') {
                    continue;
                }
                if (strpos(strtoupper($pointName), 'CONTOH:') === 0) {
                    continue;
                }
                if ($pointName === '' || $detail === '' || $description === '' || $score === null
                    || !in_array($kind, ['WHAT', 'HOW'], true)) {
                    $skipped++;
                    $errors[] = "Baris INDIKATOR TIPE A $row tidak lengkap atau tidak valid.";
                    continue;
                }

                $table = $kind === 'WHAT' ? 'tb_whats' : 'tb_hows';
                $idColumn = $kind === 'WHAT' ? 'id_what' : 'id_how';
                $typeColumn = $kind === 'WHAT' ? 'tipe_what' : 'tipe_how';
                $pointColumn = $kind === 'WHAT' ? 'p_what' : 'p_how';
                $kpiPointColumn = $kind === 'WHAT' ? 'poin' : 'poin2';
                $indicatorTable = $kind === 'WHAT' ? 'tb_indikator_whats' : 'tb_indikator_hows';
                $indicatorForeignKey = $kind === 'WHAT' ? 'id_what' : 'id_how';
                $pointNameSafe = mysqli_real_escape_string($conn, $pointName);
                $detailSafe = mysqli_real_escape_string($conn, $detail);
                $itemResult = mysqli_query(
                    $conn,
                    "SELECT item.`$idColumn` AS id_item FROM `$table` item "
                    . "JOIN tb_kpi kpi ON kpi.id=item.id_kpi AND kpi.id_user=item.id_user "
                    . "WHERE item.id_user=$targetUserId AND item.`$typeColumn`='A' "
                    . "AND kpi.`$kpiPointColumn`='$pointNameSafe' AND item.`$pointColumn`='$detailSafe' LIMIT 2"
                );
                $item = $itemResult ? mysqli_fetch_assoc($itemResult) : null;
                if (!$item || mysqli_num_rows($itemResult) > 1) {
                    $skipped++;
                    $errors[] = "Baris INDIKATOR TIPE A $row: Detail KPI tipe A tidak ditemukan atau namanya ganda.";
                    continue;
                }

                $itemId = (int) $item['id_item'];
                $descriptionSafe = mysqli_real_escape_string($conn, $description);
                $duplicateIndicator = mysqli_query(
                    $conn,
                    "SELECT id_indikator FROM `$indicatorTable` WHERE `$indicatorForeignKey`=$itemId "
                    . "AND keterangan='$descriptionSafe' LIMIT 1"
                );
                if ($duplicateIndicator && mysqli_num_rows($duplicateIndicator) > 0) {
                    $skipped++;
                    continue;
                }

                $orderResult = mysqli_query(
                    $conn,
                    "SELECT COALESCE(MAX(urutan), 0) + 1 AS next_order FROM `$indicatorTable` WHERE `$indicatorForeignKey`=$itemId"
                );
                $orderRow = $orderResult ? mysqli_fetch_assoc($orderResult) : ['next_order' => 1];
                $order = max(1, (int) $orderRow['next_order']);
                $scoreSql = number_format($score, 2, '.', '');
                $insertIndicator = mysqli_query(
                    $conn,
                    "INSERT INTO `$indicatorTable` (`$indicatorForeignKey`, keterangan, nilai, urutan, is_edited, edited_by, edited_at) "
                    . "VALUES ($itemId, '$descriptionSafe', $scoreSql, $order, 1, $editorUserId, NOW())"
                );
                if (!$insertIndicator) {
                    throw new RuntimeException("Baris INDIKATOR TIPE A $row gagal disimpan.");
                }
                $indicatorsAdded++;
            }
        }

        if ($dryRun) {
            mysqli_rollback($conn);
        } else {
            mysqli_commit($conn);
        }
    } catch (Throwable $exception) {
        mysqli_rollback($conn);
        throw $exception;
    }

    return [
        'updated' => $groupsAdded + $detailsAdded + $indicatorsAdded,
        'groups' => $groupsAdded,
        'details' => $detailsAdded,
        'indicators' => $indicatorsAdded,
        'skipped' => $skipped,
        'errors' => $errors,
    ];
}
