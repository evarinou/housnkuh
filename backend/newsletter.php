<?php
// Aktiviere Fehlerprotokollierung (in Produktion ggf. entfernen oder anpassen)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Protokolliere Fehler in eine Datei
ini_set('log_errors', 1);
ini_set('error_log', 'newsletter_error.log');

// Direkte Datenbankeinstellungen
$host = '127.0.0.1';
$port = 3307;
$database = 'yhe56tye_housnkuh';
$username = 'yhe56tye_eva';
$password = 'SherlockHolmes2!'; // Ändern Sie dies zu Ihrem DB-Passwort

// CORS-Header setzen (sichere, aber offene Konfiguration)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Protokolliere eingehende Anfragen für Debugging
$logMessage = "Anfrage empfangen: " . date('Y-m-d H:i:s') . "\n";
$logMessage .= "Methode: " . $_SERVER['REQUEST_METHOD'] . "\n";
file_put_contents('newsletter_requests.log', $logMessage, FILE_APPEND);

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

// Daten aus der Anfrage lesen (unterstützt sowohl FormData als auch JSON)
$rawData = file_get_contents('php://input');
$contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

// Protokolliere den Inhalt der Anfrage
file_put_contents('newsletter_requests.log', "Content-Type: $contentType\n", FILE_APPEND);
file_put_contents('newsletter_requests.log', "Raw data: $rawData\n", FILE_APPEND);
file_put_contents('newsletter_requests.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

try {
    // Extrahiere E-Mail und Typ abhängig vom Content-Type
    if (strpos($contentType, 'application/json') !== false) {
        // JSON-Anfrage
        $data = json_decode($rawData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Ungültiges JSON-Format: ' . json_last_error_msg());
        }
        $email = isset($data['email']) ? $data['email'] : null;
        $type = isset($data['type']) ? $data['type'] : 'customer';
    } else {
        // FormData oder andere Anfrage
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $type = isset($_POST['type']) ? $_POST['type'] : 'customer';
    }

    // Protokolliere die extrahierten Daten
    file_put_contents('newsletter_requests.log', "Extrahierte Daten: E-Mail=$email, Typ=$type\n", FILE_APPEND);

    // E-Mail validieren
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.'
        ]);
        exit;
    }

    // Typ validieren
    if (!in_array($type, ['customer', 'vendor'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Bitte wählen Sie einen gültigen Anmeldetyp.'
        ]);
        exit;
    }

    // MySQL-Verbindung herstellen
    try {
        $db = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", 
            $username, $password, 
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Tabelle erstellen falls nicht vorhanden
        $db->exec("
            CREATE TABLE IF NOT EXISTS newsletter (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                type ENUM('customer', 'vendor') NOT NULL,
                subscribedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                active BOOLEAN DEFAULT TRUE
            )
        ");
        
        // Prüfen, ob die E-Mail bereits existiert
        $stmt = $db->prepare("SELECT id FROM newsletter WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Diese E-Mail-Adresse ist bereits registriert.'
            ]);
            exit;
        }
        
        // E-Mail in die Datenbank einfügen
        $stmt = $db->prepare("INSERT INTO newsletter (email, type) VALUES (?, ?)");
        $success = $stmt->execute([$email, $type]);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Vielen Dank für Ihre Anmeldung!'
            ]);
            
            // Protokolliere den Erfolg
            file_put_contents('newsletter_requests.log', "Erfolgreiche Anmeldung für: $email\n", FILE_APPEND);
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
        file_put_contents('newsletter_requests.log', "DB Fehler: {$e->getMessage()}\n", FILE_APPEND);
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
    file_put_contents('newsletter_requests.log', "Allgemeiner Fehler: {$e->getMessage()}\n", FILE_APPEND);
}
?>