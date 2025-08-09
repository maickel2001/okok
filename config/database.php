<?php
// Load environment variables if available
$envLoader = __DIR__ . '/env.php';
if (is_readable($envLoader)) {
    require_once $envLoader;
}

// Configuration de la base de données (fallback vers valeurs existantes si .env non défini)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'u634930929_Inoo');
define('DB_USER', getenv('DB_USER') ?: 'u634930929_Inoo');
define('DB_PASS', getenv('DB_PASS') ?: 'Ino1234@');

// Configuration générale du site
define('SITE_NAME', getenv('SITE_NAME') ?: 'SMM Pro');
define('SITE_URL', getenv('SITE_URL') ?: 'https://darkgoldenrod-turkey-940813.hostingersite.com/');

define('UPLOAD_DIR', rtrim(getenv('UPLOAD_DIR') ?: 'uploads/', '/') . '/');

define('LOGS_DIR', rtrim(getenv('LOGS_DIR') ?: 'logs/', '/') . '/');

// Classe de connexion à la base de données
class Database {
    private $connection;

    public function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}

// Créer les dossiers nécessaires s'ils n'existent pas
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0777, true);
}
if (!is_dir(LOGS_DIR)) {
    @mkdir(LOGS_DIR, 0777, true);
}
?>
