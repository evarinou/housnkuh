<?php
// API-Router für Cross-Domain-Probleme und flexible Pfade

// CORS-Header für alle Anfragen
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Bei OPTIONS-Anfragen (CORS preflight) sofort beenden
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Request-URL extrahieren
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$scriptDir = dirname($scriptName);

// Extrahiere den API-Pfad nach /api/
if (preg_match('/\/api\/([^\/]+)/', $requestUri, $matches)) {
    $apiEndpoint = $matches[1];
} else {
    // Fallback: Versuchen den Endpunkt aus dem Query-String zu lesen
    $apiEndpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
}

// Definieren der verfügbaren API-Endpunkte und ihrer PHP-Dateien
$endpoints = [
    'contact' => 'contact-form.php',
    'rental-request' => 'rental-request.php',
    'newsletter' => 'newsletter.php'
];

// Mögliche Verzeichnisse, in denen sich die PHP-Dateien befinden könnten
$possibleDirs = [
    '', // Root-Verzeichnis
    'backend/', 
    'api/'
];

// Endpoint validieren
if (empty($apiEndpoint) || !isset($endpoints[$apiEndpoint])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Ungültiger API-Endpunkt'
    ]);
    exit;
}

// PHP-Datei für den Endpunkt
$phpFile = $endpoints[$apiEndpoint];

// Protokolliere den Versuch
error_log("API-Router: Versuche Weiterleitung zu Endpunkt '$apiEndpoint' ($phpFile)");

// Versuche, die PHP-Datei in verschiedenen Verzeichnissen zu finden
$fileFound = false;

foreach ($possibleDirs as $dir) {
    $filePath = __DIR__ . '/' . $dir . $phpFile;
    
    if (file_exists($filePath)) {
        error_log("API-Router: Datei gefunden in $filePath");
        
        // Daten von POST kopieren (falls sie nicht weitergeleitet werden)
        $_POST = array_merge($_POST, json_decode(file_get_contents('php://input'), true) ?: []);
        
        // Datei einbinden und ausführen
        require_once $filePath;
        $fileFound = true;
        break;
    }
}

// Wenn keine Datei gefunden wurde, Fehler zurückgeben
if (!$fileFound) {
    error_log("API-Router: Keine PHP-Datei für '$apiEndpoint' gefunden.");
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => "API-Endpunkt nicht verfügbar. Datei '$phpFile' nicht gefunden."
    ]);
}