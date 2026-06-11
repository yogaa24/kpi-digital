<?php
function verifyUserPassword($plainPassword, $storedPassword)
{
    if (isUserPasswordHash($storedPassword)) {
        return password_verify($plainPassword, $storedPassword);
    }

    return hash_equals((string) $storedPassword, (string) $plainPassword);
}

function isUserPasswordHash($storedPassword)
{
    return preg_match('/^\$(2y|2a|2b|argon2i|argon2id)\$/', (string) $storedPassword) === 1;
}

function hashUserPassword($plainPassword)
{
    return password_hash($plainPassword, PASSWORD_DEFAULT);
}

function getLevelByJabatan($jabatan)
{
    switch ($jabatan) {
        case 'Koordinator':
            return 2;
        case 'Manager':
            return 3;
        case 'Kadep':
            return 4;
        case 'Direktur':
            return 5;
        case 'Admin HRD':
            return 7;
        default:
            return 1;
    }
}
?>
