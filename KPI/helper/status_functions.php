<?php
function getStatusBadge($status) {
    switch($status) {
        case 1:
            return '<span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> Proses Review</span>';
        case 2:
            return '<span class="badge bg-info"><i class="bi bi-eye-fill"></i> Reviewed</span>';
        case 3:
            return '<span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Approved</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
}

function getStatusText($status) {
    switch($status) {
        case 1:
            return 'Proses Review';
        case 2:
            return 'Reviewed';
        case 3:
            return 'Approved';
        default:
            return 'Unknown';
    }
}

function getStatusColor($status) {
    switch($status) {
        case 1:
            return '#ffc107'; // warning - kuning
        case 2:
            return '#0dcaf0'; // info - biru muda
        case 3:
            return '#198754'; // success - hijau
        default:
            return '#6c757d'; // secondary - abu
    }
}
?>