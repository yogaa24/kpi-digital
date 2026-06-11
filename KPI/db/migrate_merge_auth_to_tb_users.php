<?php
require __DIR__ . '/../helper/config.php';
require __DIR__ . '/../helper/auth.php';

function tableExists($conn, $table)
{
    $table = mysqli_real_escape_string($conn, $table);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    return $result && mysqli_num_rows($result) > 0;
}

function columnExists($conn, $table, $column)
{
    $table = mysqli_real_escape_string($conn, $table);
    $column = mysqli_real_escape_string($conn, $column);
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && mysqli_num_rows($result) > 0;
}

function runQuery($conn, $sql)
{
    if (!mysqli_query($conn, $sql)) {
        throw new RuntimeException(mysqli_error($conn) . "\nSQL: " . $sql);
    }
}

try {
    if (!tableExists($conn, 'tb_users')) {
        throw new RuntimeException('Tabel tb_users tidak ditemukan.');
    }

    if (!columnExists($conn, 'tb_users', 'password')) {
        runQuery($conn, "ALTER TABLE tb_users ADD COLUMN password varchar(255) NULL AFTER username");
    }

    if (!columnExists($conn, 'tb_users', 'level')) {
        runQuery($conn, "ALTER TABLE tb_users ADD COLUMN level int NULL AFTER password");
    }

    $migratedFromAuth = 0;
    $hashedFromUsers = 0;
    if (tableExists($conn, 'tb_auth')) {
        $result = mysqli_query($conn, "SELECT id_user, password, level FROM tb_auth");
        if (!$result) {
            throw new RuntimeException(mysqli_error($conn));
        }

        while ($auth = mysqli_fetch_assoc($result)) {
            $idUser = (int) $auth['id_user'];
            $storedPassword = (string) $auth['password'];
            $hashedPassword = !isUserPasswordHash($storedPassword)
                ? hashUserPassword($storedPassword)
                : $storedPassword;
            $hashedPassword = mysqli_real_escape_string($conn, $hashedPassword);
            $level = (int) $auth['level'];

            runQuery($conn, "UPDATE tb_users SET password='$hashedPassword', level=$level WHERE id=$idUser");
            $migratedFromAuth++;
        }

        $backupTable = 'tb_auth_backup_' . date('Ymd_His');
        runQuery($conn, "CREATE TABLE `$backupTable` AS SELECT * FROM tb_auth");
        runQuery($conn, "DROP TABLE tb_auth");
    }

    $result = mysqli_query($conn, "SELECT id, password FROM tb_users");
    if (!$result) {
        throw new RuntimeException(mysqli_error($conn));
    }

    while ($user = mysqli_fetch_assoc($result)) {
        if (isUserPasswordHash($user['password'])) {
            continue;
        }

        $idUser = (int) $user['id'];
        $hashedPassword = mysqli_real_escape_string($conn, hashUserPassword((string) $user['password']));
        runQuery($conn, "UPDATE tb_users SET password='$hashedPassword' WHERE id=$idUser");
        $hashedFromUsers++;
    }

    runQuery($conn, "UPDATE tb_users SET level = CASE
        WHEN jabatan = 'Admin HRD' THEN 7
        WHEN jabatan = 'Direktur' THEN 5
        WHEN jabatan = 'Kadep' THEN 4
        WHEN jabatan = 'Manager' THEN 3
        WHEN jabatan = 'Koordinator' THEN 2
        ELSE 1
    END WHERE level IS NULL");

    runQuery($conn, "UPDATE tb_users SET password = '" . mysqli_real_escape_string($conn, hashUserPassword('123456')) . "' WHERE password IS NULL OR password = ''");
    runQuery($conn, "ALTER TABLE tb_users MODIFY password varchar(255) NOT NULL");
    runQuery($conn, "ALTER TABLE tb_users MODIFY level int NOT NULL DEFAULT 1");

    echo "Migrasi selesai.\n";
    echo "Password dari tb_auth dipindah ke tb_users: $migratedFromAuth user.\n";
    echo "Password plaintext di tb_users yang di-hash: $hashedFromUsers user.\n";
    echo "Jika tb_auth ada, tabel tersebut sudah dibackup lalu dihapus.\n";
    echo "User tanpa password diberi default sementara: 123456\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Migrasi gagal:\n" . $e->getMessage() . "\n";
    exit(1);
}
