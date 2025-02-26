<?php
// Einfacher Formular-Handler für alle Formen von Anfragen

// CORS-Header setzen
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Bei OPTIONS-Anfrage sofort beenden
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Fehlerprotokollierung aktivieren
ini_set('log_errors', 1);
ini_set('error_log', 'form_handler_error.log');

// Protokolliere eingehende Anfrage
$log_message = date('Y-m-d H:i:s') . " - Neue Anfrage:\n";
$log_message .= "HTTP-Methode: " . $_SERVER['REQUEST_METHOD'] . "\n";
$log_message .= "Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'nicht angegeben') . "\n";
$log_message .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Formulartyp aus dem Request ermitteln
$form_type = isset($_GET['type']) ? $_GET['type'] : 'unknown';

// Daten aus verschiedenen Quellen extrahieren
$post_data = $_POST;
$json_data = json_decode(file_get_contents('php://input'), true) ?: [];
$data = array_merge($post_data, $json_data);

// Protokolliere die Daten
$log_message .= "Formulartyp: " . $form_type . "\n";
$log_message .= "Daten: " . print_r($data, true) . "\n";

file_put_contents('form_submissions.log', $log_message . "\n\n", FILE_APPEND);

// E-Mail-Empfänger
$to = 'eva-maria.schaller@housnkuh.de';

// Basierend auf dem Formulartyp unterschiedliche Aktionen ausführen
switch ($form_type) {
    case 'contact':
        $name = $data['name'] ?? 'Unbekannt';
        $email = $data['email'] ?? 'keine@angabe.de';
        $subject = $data['subject'] ?? 'Kontaktanfrage';
        $message = $data['message'] ?? 'Keine Nachricht';
        $phone = $data['phone'] ?? 'Nicht angegeben';
        
        // E-Mail-Betreff und Inhalt erstellen
        $email_subject = "[housnkuh Kontakt] $subject";
        $email_body = "Name: $name\n";
        $email_body .= "E-Mail: $email\n";
        $email_body .= "Telefon: $phone\n";
        $email_body .= "Betreff: $subject\n\n";
        $email_body .= "Nachricht:\n$message\n";
        break;
        
    case 'rental':
        $businessName = $data['businessName'] ?? 'Unbekannt';
        $contactPerson = $data['contactPerson'] ?? 'Unbekannt';
        $email = $data['email'] ?? 'keine@angabe.de';
        $phone = $data['phone'] ?? 'Nicht angegeben';
        $productType = $data['productType'] ?? 'Nicht angegeben';
        $spaceType = $data['spaceType'] ?? 'Nicht angegeben';
        $message = $data['message'] ?? '';
        
        // Mapping der Verkaufsflächentypen
        $spaceTypeNames = [
            'regal-a' => 'Verkaufsblock Lage A (35€/Monat)',
            'regal-b' => 'Verkaufsblock Lage B (15€/Monat)',
            'kuehl' => 'Verkaufsblock gekühlt (50€/Monat)',
            'tisch' => 'Verkaufsblock Tisch (40€/Monat)'
        ];
        
        $spaceTypeName = isset($spaceTypeNames[$spaceType]) ? $spaceTypeNames[$spaceType] : $spaceType;
        
        // E-Mail-Betreff und Inhalt erstellen
        $email_subject = "[housnkuh Mietanfrage] $businessName - $spaceTypeName";
        $email_body = "Neue Mietanfrage über die Website:\n\n";
        $email_body .= "Firmenname: $businessName\n";
        $email_body .= "Ansprechpartner: $contactPerson\n";
        $email_body .= "E-Mail: $email\n";
        $email_body .= "Telefon: $phone\n";
        $email_body .= "Art der Produkte: $productType\n";
        $email_body .= "Gewünschte Verkaufsfläche: $spaceTypeName\n";
        
        if (!empty($message)) {
            $email_body .= "\nNachricht:\n$message\n";
        }
        break;
        
    case 'vendor-contest':
        $name = $data['name'] ?? 'Unbekannt';
        $email = $data['email'] ?? 'keine@angabe.de';
        $phone = $data['phone'] ?? 'Nicht angegeben';
        $vendor1 = $data['guessedVendors'][0] ?? 'Nicht angegeben';
        $vendor2 = $data['guessedVendors'][1] ?? 'Nicht angegeben';
        $vendor3 = $data['guessedVendors'][2] ?? 'Nicht angegeben';
        
        // E-Mail-Betreff und Inhalt erstellen
        $email_subject = "[housnkuh Wettbewerb] Neue Teilnahme von $name";
        $email_body = "Neue Wettbewerbsteilnahme:\n\n";
        $email_body .= "Name: $name\n";
        $email_body .= "E-Mail: $email\n";
        $email_body .= "Telefon: $phone\n";
        $email_body .= "\nVermutete Direktvermarkter:\n";
        $email_body .= "1. $vendor1\n";
        $email_body .= "2. $vendor2\n";
        $email_body .= "3. $vendor3\n";
        
        // Optional: In Datenbank speichern
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
            
            // Eintrag in die Datenbank einfügen
            $stmt = $pdo->prepare("
                INSERT INTO vendor_contest (name, email, phone, vendor1, vendor2, vendor3, ip_address)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $name, 
                $email, 
                $phone, 
                $vendor1, 
                $vendor2, 
                $vendor3, 
                $_SERVER['REMOTE_ADDR']
            ]);
            
        } catch (PDOException $e) {
            // Fehler protokollieren, aber fortfahren
            file_put_contents('form_submissions.log', "DB-Fehler beim Speichern von VendorContest: {$e->getMessage()}\n", FILE_APPEND);
        }
        break;
        
    default:
        // Allgemeiner Fall
        $email_subject = "[housnkuh Website] Neue Formularanfrage";
        $email_body = "Neue Anfrage über die Website:\n\n";
        
        foreach ($data as $key => $value) {
            $email_body .= "$key: $value\n";
        }
}

// E-Mail-Header
$headers = "From: noreply@housnkuh.de\r\n";
$headers .= "Reply-To: " . ($email ?? 'noreply@housnkuh.de') . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// E-Mail senden
$mail_sent = mail($to, $email_subject, $email_body, $headers);

if ($mail_sent) {
    // Protokolliere Erfolg
    file_put_contents('form_submissions.log', "E-Mail erfolgreich gesendet!\n\n", FILE_APPEND);
    
    echo json_encode([
        'success' => true,
        'message' => 'Vielen Dank für Ihre Nachricht! Wir werden uns so schnell wie möglich bei Ihnen melden.'
    ]);
} else {
    // Protokolliere Fehler
    file_put_contents('form_submissions.log', "Fehler beim Senden der E-Mail!\n\n", FILE_APPEND);
    
    echo json_encode([
        'success' => false,
        'message' => 'Es gab ein Problem beim Senden Ihrer Nachricht. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns direkt.',
        'technical_info' => error_get_last()
    ]);
}