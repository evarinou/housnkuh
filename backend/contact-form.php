<?php
// Aktiviere Fehlerprotokollierung (in Produktion ggf. entfernen oder anpassen)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Protokolliere Fehler in eine Datei
ini_set('log_errors', 1);
ini_set('error_log', 'contact_form_error.log');

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

// Protokolliere eingehende Anfragen für Debugging
$logMessage = "Kontaktanfrage empfangen: " . date('Y-m-d H:i:s') . "\n";
file_put_contents('contact_requests.log', $logMessage, FILE_APPEND);

// Daten aus der Anfrage lesen (unterstützt sowohl FormData als auch JSON)
$rawData = file_get_contents('php://input');
$contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

try {
    // E-Mail-Einstellungen
    $to = 'eva-maria.schaller@housnkuh.de';
    
    // Extrahiere Daten abhängig vom Content-Type
    if (strpos($contentType, 'application/json') !== false) {
        // JSON-Anfrage
        $data = json_decode($rawData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Ungültiges JSON-Format: ' . json_last_error_msg());
        }
        
        $name = isset($data['name']) ? filter_var($data['name'], FILTER_SANITIZE_STRING) : '';
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : '';
        $phone = isset($data['phone']) ? filter_var($data['phone'], FILTER_SANITIZE_STRING) : '';
        $subject = isset($data['subject']) ? filter_var($data['subject'], FILTER_SANITIZE_STRING) : 'Kontaktanfrage';
        $message = isset($data['message']) ? filter_var($data['message'], FILTER_SANITIZE_STRING) : '';
    } else {
        // FormData oder andere Anfrage
        $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : '';
        $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
        $phone = isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : '';
        $subject = isset($_POST['subject']) ? filter_var($_POST['subject'], FILTER_SANITIZE_STRING) : 'Kontaktanfrage';
        $message = isset($_POST['message']) ? filter_var($_POST['message'], FILTER_SANITIZE_STRING) : '';
    }
    
    // Validierung der Eingaben
    if (empty($name) || empty($email) || empty($message)) {
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
    
    // E-Mail-Inhalt erstellen
    $emailSubject = "[housnkuh Kontakt] $subject";
    
    $emailBody = "Name: $name\n";
    $emailBody .= "E-Mail: $email\n";
    
    if (!empty($phone)) {
        $emailBody .= "Telefon: $phone\n";
    }
    
    $emailBody .= "Betreff: $subject\n\n";
    $emailBody .= "Nachricht:\n$message\n";
    
    // E-Mail-Header
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // E-Mail senden
    $mailSent = mail($to, $emailSubject, $emailBody, $headers);
    
    if ($mailSent) {
        // E-Mail erfolgreich gesendet
        echo json_encode([
            'success' => true,
            'message' => 'Vielen Dank für Ihre Nachricht! Wir werden uns so schnell wie möglich bei Ihnen melden.'
        ]);
        
        // In Datenbank speichern für Archivierung (optional)
        try {
            // Konfigurationsdatei einbinden
            require_once dirname(__FILE__) . '/config.php';
            
            // Datenbankverbindung herstellen
            $pdo = new PDO(
                "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset=utf8mb4",
                $dbConfig['username'],
                $dbConfig['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // Tabelle erstellen, falls nicht vorhanden
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS contact_messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    phone VARCHAR(50),
                    subject VARCHAR(255) NOT NULL,
                    message TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Eintrag in die Datenbank einfügen
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, phone, subject, message)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            
            // Erfolg protokollieren
            file_put_contents('contact_requests.log', "Kontaktanfrage gespeichert: $email\n", FILE_APPEND);
        } catch (PDOException $e) {
            // Fehler bei der Datenbankverbindung protokollieren, aber nicht an den Benutzer zurückgeben
            file_put_contents('contact_requests.log', "DB-Fehler beim Speichern: {$e->getMessage()}\n", FILE_APPEND);
        }
    } else {
        // Fehler beim Senden der E-Mail
        throw new Exception('E-Mail konnte nicht gesendet werden.');
    }
} catch (Exception $e) {
    // Protokolliere den Fehler
    file_put_contents('contact_requests.log', "Fehler: {$e->getMessage()}\n", FILE_APPEND);
    
    // Fehler an den Client zurückgeben
    echo json_encode([
        'success' => false,
        'message' => 'Bei der Übermittlung Ihrer Nachricht ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns telefonisch.'
    ]);
}