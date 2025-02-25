<?php
// Aktiviere Fehlerprotokollierung (in Produktion ggf. entfernen oder anpassen)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Protokolliere Fehler in eine Datei
ini_set('log_errors', 1);
ini_set('error_log', 'vendor_contest_error.log');

// Direkte Datenbankeinstellungen
$host = '127.0.0.1';
$port = 3307;
$database = 'yhe56tye_housnkuh';
$username = 'yhe56tye_eva';
$password = 'SherlockHolmes2!'; 

// CORS-Header setzen
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Protokolliere eingehende Anfragen für Debugging
$logMessage = "Anfrage empfangen: " . date('Y-m-d H:i:s') . "\n";
$logMessage .= "Methode: " . $_SERVER['REQUEST_METHOD'] . "\n";
file_put_contents('vendor_contest_requests.log', $logMessage, FILE_APPEND);

// Bei OPTIONS-Anfrage sofort beenden (für CORS Preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Nur POST-Anfragen akzeptieren
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Nur POST-Anfragen sind erlaubt.'
    ]);
    exit;
}

// Daten aus der Anfrage lesen
$rawData = file_get_contents('php://input');
$contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

// Protokolliere den Inhalt der Anfrage
file_put_contents('vendor_contest_requests.log', "Content-Type: $contentType\n", FILE_APPEND);
file_put_contents('vendor_contest_requests.log', "Raw data: $rawData\n", FILE_APPEND);
file_put_contents('vendor_contest_requests.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

try {
    // Extrahiere Daten je nach Content-Type
    if (strpos($contentType, 'application/json') !== false) {
        // JSON-Anfrage
        $data = json_decode($rawData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Ungültiges JSON-Format: ' . json_last_error_msg());
        }
    } else {
        // FormData oder andere Anfrage
        $data = $_POST;
    }
    
    // Protokolliere die extrahierten Daten
    file_put_contents('vendor_contest_requests.log', "Extrahierte Daten: " . print_r($data, true) . "\n", FILE_APPEND);
    
    // Pflichtfelder validieren
    if (empty($data['name']) || empty($data['email']) || empty($data['guessedVendors']) || count($data['guessedVendors']) < 3) {
        echo json_encode([
            'success' => false,
            'message' => 'Bitte füllen Sie alle Pflichtfelder aus.'
        ]);
        exit;
    }
    
    // E-Mail validieren
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.'
        ]);
        exit;
    }
    
    // Telefonnummer (optional)
    $phone = isset($data['phone']) ? $data['phone'] : '';
    
    // Vermutete Direktvermarkter
    $vendor1 = $data['guessedVendors'][0];
    $vendor2 = $data['guessedVendors'][1];
    $vendor3 = $data['guessedVendors'][2];
    
    // MySQL-Verbindung herstellen
    try {
        $db = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", 
            $username, $password, 
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Tabelle erstellen falls nicht vorhanden
        $db->exec("
            CREATE TABLE IF NOT EXISTS vendor_contest (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                vendor1 VARCHAR(255) NOT NULL,
                vendor2 VARCHAR(255) NOT NULL,
                vendor3 VARCHAR(255) NOT NULL,
                submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ip_address VARCHAR(45)
            )
        ");
        
        // Prüfen, ob die E-Mail bereits teilgenommen hat
        $stmt = $db->prepare("SELECT id FROM vendor_contest WHERE email = ?");
        $stmt->execute([$data['email']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Sie haben bereits am Wettbewerb teilgenommen.'
            ]);
            exit;
        }
        
        // IP-Adresse speichern (für Missbrauchserkennung)
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Daten in die Datenbank einfügen
        $stmt = $db->prepare("
            INSERT INTO vendor_contest (name, email, phone, vendor1, vendor2, vendor3, ip_address) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $success = $stmt->execute([
            $data['name'], 
            $data['email'], 
            $phone, 
            $vendor1, 
            $vendor2, 
            $vendor3, 
            $ip
        ]);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Vielen Dank für Ihre Teilnahme!'
            ]);
            
            // Protokolliere den Erfolg
            file_put_contents('vendor_contest_requests.log', "Erfolgreiche Teilnahme für: {$data['email']}\n", FILE_APPEND);
        } else {
            throw new Exception('Datenbankfehler beim Einfügen');
        }
    } catch (PDOException $e) {
        // Spezifischer Datenbankfehler
        $errorMessage = 'Datenbankfehler: ' . $e->getMessage();
        error_log($errorMessage);
        
        echo json_encode([
            'success' => false,
            'message' => 'Ein Datenbankfehler ist aufgetreten. Bitte versuchen Sie es später erneut.'
        ]);
        
        // Protokolliere den Fehler
        file_put_contents('vendor_contest_requests.log', "DB Fehler: {$e->getMessage()}\n", FILE_APPEND);
    }
} catch (Exception $e) {
    // Allgemeiner Fehler
    $errorMessage = 'Fehler: ' . $e->getMessage();
    error_log($errorMessage);
    
    echo json_encode([
        'success' => false,
        'message' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.'
    ]);
    
    // Protokolliere den Fehler
    file_put_contents('vendor_contest_requests.log', "Allgemeiner Fehler: {$e->getMessage()}\n", FILE_APPEND);
}
?>