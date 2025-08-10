<?php
// Admin Diagnostics - No DB dependency
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$envLoader = __DIR__ . '/../config/env.php';
if (is_readable($envLoader)) { require_once $envLoader; }

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$derivedBase = $scheme . '://' . $host;

if (!defined('SITE_URL')) { define('SITE_URL', getenv('SITE_URL') ?: $derivedBase); }
if (!defined('SITE_NAME')) { define('SITE_NAME', getenv('SITE_NAME') ?: 'SMM Pro'); }
if (!defined('UPLOAD_DIR')) { define('UPLOAD_DIR', rtrim(getenv('UPLOAD_DIR') ?: 'uploads/', '/') . '/'); }
if (!defined('LOGS_DIR')) { define('LOGS_DIR', rtrim(getenv('LOGS_DIR') ?: 'logs/', '/') . '/'); }

// Restrict to admins
if (!isset($_SESSION['admin_id'])) {
    $base = rtrim(SITE_URL, '/');
    header('Location: ' . $base . '/admin/login.php');
    exit();
}

$results = [];
function addCheck(&$arr, $label, $ok, $message, $suggestion = '') {
    $arr[] = [ 'label' => $label, 'ok' => (bool)$ok, 'message' => $message, 'suggestion' => $suggestion ];
}

// PHP version
$phpOk = version_compare(PHP_VERSION, '7.4.0', '>=');
addCheck($results, 'Version PHP', $phpOk, 'PHP ' . PHP_VERSION, 'Hébergeur: utilisez PHP 7.4+');

// Extensions
$exts = [ 'pdo', 'pdo_mysql', 'gd', 'json', 'curl', 'fileinfo' ];
foreach ($exts as $ext) {
    addCheck($results, 'Extension ' . $ext, extension_loaded($ext), extension_loaded($ext) ? 'Chargée' : 'Manquante', extension_loaded($ext) ? '' : 'Activez ' . $ext . ' dans votre hébergeur');
}

// Session
$savePath = ini_get('session.save_path');
$sessionWritable = $savePath ? is_writable($savePath) : true;
addCheck($results, 'Session active', session_status() === PHP_SESSION_ACTIVE, 'session_id=' . session_id());
addCheck($results, 'session.save_path', $sessionWritable, $savePath ?: '(non défini)', $sessionWritable ? '' : 'Rendre ' . $savePath . ' inscriptible');
addCheck($results, 'Admin connecté', isset($_SESSION['admin_id']), isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : '');

// Dossiers et permissions
$root = realpath(__DIR__ . '/..');
$paths = [
    ['label' => 'Racine du site', 'path' => $root],
    ['label' => 'config/', 'path' => realpath(__DIR__ . '/../config') ?: (__DIR__ . '/../config')],
    ['label' => 'uploads/', 'path' => realpath(__DIR__ . '/../' . UPLOAD_DIR) ?: (__DIR__ . '/../' . UPLOAD_DIR)],
    ['label' => 'logs/', 'path' => realpath(__DIR__ . '/../' . LOGS_DIR) ?: (__DIR__ . '/../' . LOGS_DIR)],
];
foreach ($paths as $p) {
    $exists = file_exists($p['path']);
    $writable = $exists ? is_writable($p['path']) : is_writable(dirname($p['path']));
    $msg = ($exists ? 'Existe' : 'Absent') . ' — ' . ($writable ? 'Écriture OK' : 'Écriture refusée');
    $suggest = $writable ? '' : 'CHMOD 775/777 sur le dossier concerné';
    addCheck($results, $p['label'], $exists && $writable, $msg, $suggest);
}

// Test écriture config/local_settings.php
$cfgTarget = __DIR__ . '/../config/local_settings.php';
$cfgDir = dirname($cfgTarget);
$canWriteCfg = (is_dir($cfgDir) || @mkdir($cfgDir, 0775, true)) && @file_put_contents($cfgTarget . '.check', 'ok') !== false;
if ($canWriteCfg) { @unlink($cfgTarget . '.check'); }
addCheck($results, 'Écriture config/local_settings.php', $canWriteCfg, $canWriteCfg ? 'OK' : 'Impossible', $canWriteCfg ? '' : 'Rendre config/ inscriptible');

// SITE_URL & UPLOAD_DIR
$baseUrl = rtrim(SITE_URL, '/');
$uploadDir = UPLOAD_DIR;
$uploadPublic = (preg_match('#^https?://#', $uploadDir)) ? $uploadDir : ($baseUrl . '/' . ltrim($uploadDir, '/'));
addCheck($results, 'SITE_URL', (bool)$baseUrl, $baseUrl, 'Mettez votre domaine réel');
addCheck($results, 'UPLOAD_DIR', true, $uploadDir . ' → ' . $uploadPublic);

// DB test via getenv (sans require DB)
$dbHost = getenv('DB_HOST');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');
if ($dbHost && $dbName && $dbUser !== false) {
    try {
        $pdo = new PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8mb4', $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $ver = $pdo->query('SELECT VERSION() as v')->fetch(PDO::FETCH_ASSOC);
        addCheck($results, 'Connexion base de données', true, 'OK — MySQL ' . ($ver['v'] ?? ''));
    } catch (Throwable $e) {
        addCheck($results, 'Connexion base de données', false, $e->getMessage(), 'Vérifiez DB_HOST/DB_NAME/DB_USER/DB_PASS');
    }
} else {
    addCheck($results, 'Connexion base de données', false, 'Paramètres DB manquants', 'Définir DB_* dans .env ou config');
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostics - Admin <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="<?php echo $baseUrl; ?>/admin/dashboard.php" class="logo">
                    <i class="fas fa-stethoscope"></i>
                    Diagnostics - Admin
                </a>
                <ul class="nav-links">
                    <li><a href="<?php echo $baseUrl; ?>/admin/dashboard.php">Dashboard</a></li>
                    <li><a href="<?php echo $baseUrl; ?>/logout.php" class="btn btn-secondary">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="card" style="margin-bottom: 1.5rem;">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">
                    <i class="fas fa-info-circle"></i> Résumé
                </h2>
                <p style="color: var(--text-secondary);">
                    Cette page vérifie la configuration serveur, les extensions PHP, les permissions de fichiers et la connexion base de données.
                    Utilisez ces informations pour corriger les erreurs et éviter les pages blanches.
                </p>
            </div>

            <div class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                    <i class="fas fa-list-check"></i> Vérifications
                </h3>
                <div style="display: grid; gap: 0.75rem;">
                    <?php foreach ($results as $r): ?>
                        <div style="display:flex; justify-content: space-between; align-items: center; border:1px solid var(--border-color); border-radius:8px; padding:0.75rem; background: var(--secondary-color);">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <i class="fas <?php echo $r['ok'] ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>" style="color: <?php echo $r['ok'] ? 'var(--success-color)' : 'var(--error-color)'; ?>;"></i>
                                <strong><?php echo htmlspecialchars($r['label']); ?></strong>
                            </div>
                            <div style="text-align:right;">
                                <div><?php echo htmlspecialchars($r['message']); ?></div>
                                <?php if (!$r['ok'] && $r['suggestion']): ?>
                                    <div style="color: var(--warning-color); font-size:0.9rem;">💡 <?php echo htmlspecialchars($r['suggestion']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card" style="margin-top: 1.5rem;">
                <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                    <i class="fas fa-tools"></i> Actions rapides
                </h3>
                <ul style="color: var(--text-secondary); line-height:1.8; margin:0; padding-left:1.2rem;">
                    <li>Mettre à jour <strong>SITE_URL</strong> & <strong>UPLOAD_DIR</strong> dans <code>.env</code> ou via la page Paramètres.</li>
                    <li>Donner les droits en écriture au dossier <code>config/</code> pour sauvegarder les réglages.</li>
                    <li>Activer les extensions manquantes dans votre panel d’hébergement.</li>
                    <li>Recharger la page après corrections, ou vider le cache (OPcache/CDN).</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>