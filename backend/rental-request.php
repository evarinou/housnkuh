<?php
// Aktiviere Fehlerprotokollierung (in Produktion ggf. entfernen oder anpassen)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Protokolliere Fehler in eine Datei
ini_set('log_errors', 1);
ini_set('error_log', 'rental_request_error.log');

// CORS-Header setzen
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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

try {
    // Konfigurationsdatei einbinden
    require_once dirname(__FILE__) . '/config.php';
    
    // E-Mail-Einstellungen
    $to = 'eva-maria.schaller@housnkuh.de';
    
    // Extrahiere Daten abhängig vom Content-Type
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
    
    // Daten extrahieren und sanitieren
    $businessName = isset($data['businessName']) ? filter_var($data['businessName'], FILTER_SANITIZE_STRING) : '';
    $contactPerson = isset($data['contactPerson']) ? filter_var($data['contactPerson'], FILTER_SANITIZE_STRING) : '';
    $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($data['phone']) ? filter_var($data['phone'], FILTER_SANITIZE_STRING) : '';
    $productType = isset($data['productType']) ? filter_var($data['productType'], FILTER_SANITIZE_STRING) : '';
    $spaceType = isset($data['spaceType']) ? filter_var($data['spaceType'], FILTER_SANITIZE_STRING) : '';
    $message = isset($data['message']) ? filter_var($data['message'], FILTER_SANITIZE_STRING) : '';
    
    // Validierung der Eingaben
    if (empty($businessName) || empty($contactPerson) || empty($email) || empty($productType) || empty($spaceType)) {
        echo json_encode([
            'success' => false,
            'message' => 'Bitte füllen Sie alle erforderlichen Felder aus.'
        ]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.'
        ]);
        exit;
    }
    
    // Zuweisung des Miettypnamens basierend auf dem Wert
    $spaceTypeNames = [
        'regal-a' => 'Verkaufsblock Lage A (35€/Monat)',
        'regal-b' => 'Verkaufsblock Lage B (15€/Monat)',
        'kuehl' => 'Verkaufsblock gekühlt (50€/Monat)',
        'tisch' => 'Verkaufsblock Tisch (40€/Monat)'
    ];
    
    $spaceTypeName = isset($spaceTypeNames[$spaceType]) ? $spaceTypeNames[$spaceType] : $spaceType;
    
    // E-Mail-Inhalt erstellen
    $emailSubject = "[housnkuh Mietanfrage] $businessName - $spaceTypeName";
    
    $emailBody = "Neue Mietanfrage über die Website:\n\n";
    $emailBody .= "Firmenname: $businessName\n";
    $emailBody .= "Ansprechpartner: $contactPerson\n";
    $emailBody .= "E-Mail: $email\n";
    
    if (!empty($phone)) {
        $emailBody .= "Telefon: $phone\n";
    }
    
    $emailBody .= "Art der Produkte: $productType\n";
    $emailBody .= "Gewünschte Verkaufsfläche: $spaceTypeName\n";
    
    if (!empty($message)) {
        $emailBody .= "\nNachricht:\n$message\n";
    }
    
    // E-Mail-Header
    $headers = "From: noreply@housnkuh.de\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // E-Mail senden
    $mailSent = mail($to, $emailSubject, $emailBody, $headers);
    
    if ($mailSent) {
        // E-Mail erfolgreich gesendet
        echo json_encode([
            'success' => true,
            'message' => 'Vielen Dank für Ihre Mietanfrage! Wir werden uns so schnell wie möglich bei Ihnen melden.'
        ]);
        
        // In Datenbank speichern
        try {
            // Datenbankverbindung herstellen
            $pdo = new PDO(
                "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset=utf8mb4",
                $dbConfig['username'],
                $dbConfig['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // Tabelle erstellen, falls nicht vorhanden
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS rental_requests (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    business_name VARCHAR(255) NOT NULL,
                    contact_person VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    phone VARCHAR(50),
                    product_type VARCHAR(255) NOT NULL,
                    space_type VARCHAR(50) NOT NULL,
                    message TEXT,
                    status ENUM('new', 'contacted', 'approved', 'rejected') DEFAULT 'new',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Eintrag in die Datenbank einfügen
            $stmt = $pdo->prepare("
                INSERT INTO rental_requests 
                (business_name, contact_person, email, phone, product_type, space_type, message)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$businessName, $contactPerson, $email, $phone, $productType, $spaceType, $message]);
            
            // Erfolg protokollieren
            file_put_contents('rental_requests.log', "Mietanfrage gespeichert: $businessName - $email\n", FILE_APPEND);
        } catch (PDOException $e) {
            // Fehler bei der Datenbankverbindung protokollieren, aber nicht an den Benutzer zurückgeben
            file_put_contents('rental_requests.log', "DB-Fehler beim Speichern: {$e->getMessage()}\n", FILE_APPEND);
        }
    } else {
        // Fehler beim Senden der E-Mail
        throw new Exception('E-Mail konnte nicht gesendet werden.');
    }
} catch (Exception $e) {
    // Protokolliere den Fehler
    file_put_contents('rental_requests.log', "Fehler: {$e->getMessage()}\n", FILE_APPEND);
    
    // Fehler an den Client zurückgeben
    echo json_encode([
        'success' => false,
        'message' => 'Bei der Übermittlung Ihrer Anfrage ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns telefonisch unter 0157 35711257.'
    ]);
}