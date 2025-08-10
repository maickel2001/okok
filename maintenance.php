<?php
// Maintenance gateway and refund banner injector
$maintenanceFlag = isset($_GET['maintenance']) && $_GET['maintenance'] === 'on';
$flagFile = __DIR__ . '/MAINTENANCE';
if ($maintenanceFlag || file_exists($flagFile)) {
    $file = __DIR__ . '/maintenance.html';
    if (is_readable($file)) {
        header('Content-Type: text/html; charset=UTF-8');
        readfile($file);
        exit;
    } else {
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Maintenance en cours. Revenez bientôt.";
        exit;
    }
}

// Helper to render refund policy banner
function refund_banner() {
    echo '<div style="position:fixed;bottom:12px;left:50%;transform:translateX(-50%);z-index:9999;background:rgba(255,255,255,0.08);'
       . 'border:1px solid rgba(255,255,255,0.12);padding:10px 14px;border-radius:999px;color:#b5b5b7;font-size:12px;backdrop-filter:saturate(180%) blur(12px)">'
       . '<i class="fas fa-shield-alt" style="color:#ff7a00"></i> Satisfait ou remboursé sous 7 jours selon conditions'
       . '</div>';
}