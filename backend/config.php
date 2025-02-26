<?php
// config.php
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos(trim($line), '#') !== 0) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            if (!empty($name)) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
            }
        }
    }
    return true;
}

// Ermittle die Umgebung (lokal oder Produktion)
$isLocalDevelopment = strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false || 
                     strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false;

// Lade die entsprechende .env Datei
$envPath = $isLocalDevelopment 
    ? __DIR__ . '/.env.development' 
    : __DIR__ . '/.env';

loadEnv($envPath);

// Datenbank-Konfiguration
$dbConfig = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '3307',
    'dbname' => getenv('DB_NAME') ?: 'yhe56tye_housnkuh',
    'username' => getenv('DB_USER') ?: 'yhe56tye_eva',
    'password' => getenv('DB_PASSWORD') ?: 'SherlockHolmes2!',
];

// Admin-Konfiguration
$adminConfig = [
    'password' => getenv('ADMIN_PASSWORD') ?: 'admin'
];
?>