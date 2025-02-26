<?php
// config.php - Verbesserte Version mit besserer Fehlerbehandlung

// Funktion zum Laden von Umgebungsvariablen aus einer .env-Datei
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

// Ermittle den Host
$serverHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

// Bestimme, ob wir in einer lokalen Entwicklungsumgebung sind
$isLocalDevelopment = strpos($serverHost, 'localhost') !== false || 
                     strpos($serverHost, '127.0.0.1') !== false;

// Lade die entsprechende .env Datei
$envPath = $isLocalDevelopment 
    ? __DIR__ . '/.env.development' 
    : __DIR__ . '/.env';

// Versuche die .env-Datei zu laden, falls sie existiert
$envLoaded = loadEnv($envPath);

// Protokolliere, ob die .env-Datei geladen wurde (für Debugging)
error_log("Env file {$envPath} loaded: " . ($envLoaded ? "yes" : "no"));

// Datenbank-Konfiguration mit Fallback-Werten für die Produktion
$dbConfig = [
    // Bei Hosting-Providern ist oft 'localhost' statt '127.0.0.1' erforderlich
    'host' => getenv('DB_HOST') ?: 'localhost',
    
    // Standard MySQL-Port, falls nicht anders angegeben
    'port' => getenv('DB_PORT') ?: '3307',
    
    // Datenbank-Namen aus der Umgebungsvariable oder Fallback
    'dbname' => getenv('DB_NAME') ?: 'yhe56tye_housnkuh',
    
    // Datenbank-Benutzer aus der Umgebungsvariable oder Fallback
    'username' => getenv('DB_USER') ?: 'yhe56tye_eva',
    
    // Datenbank-Passwort aus der Umgebungsvariable oder Fallback
    'password' => getenv('DB_PASSWORD') ?: 'SherlockHolmes2!',
];

// Admin-Konfiguration
$adminConfig = [
    'password' => getenv('ADMIN_PASSWORD') ?: 'admin'
];

// Protokolliere die Konfiguration für Debugging (ohne Passwort)
error_log("DB Config: host={$dbConfig['host']}, port={$dbConfig['port']}, dbname={$dbConfig['dbname']}, user={$dbConfig['username']}");

/**
 * Hilfsfunktion für die Datenbankverbindung mit besserer Fehlerbehandlung
 */
function getDbConnection() {
    global $dbConfig;
    
    try {
        // DSN mit oder ohne Port, je nach Konfiguration
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4";
        
        // Port nur hinzufügen, wenn nicht der Standard-Port
        if (isset($dbConfig['port']) && $dbConfig['port'] != '3306') {
            $dsn .= ";port={$dbConfig['port']}";
        }
        
        $pdo = new PDO(
            $dsn,
            $dbConfig['username'],
            $dbConfig['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        return $pdo;
    } catch (PDOException $e) {
        // Protokolliere den Fehler, aber keine sensiblen Daten
        error_log("Database connection error: " . $e->getMessage());
        
        // Wirf den Fehler erneut, damit er von der aufrufenden Funktion behandelt werden kann
        throw $e;
    }
}