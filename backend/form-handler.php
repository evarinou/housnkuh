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

// Daten für universelle Datenbankverbindung vorbereiten
$email = '';
$name = '';
$phone = '';
$summary = '';

// Basierend auf dem Formulartyp unterschiedliche Aktionen ausführen
switch ($form_type) {
    case 'contact':
        $name = $data['name'] ?? 'Unbekannt';
        $email = $data['email'] ?? 'keine@angabe.de';
        $subject = $data['subject'] ?? 'Kontaktanfrage';
        $message = $data['message'] ?? 'Keine Nachricht';
        $phone = $data['phone'] ?? 'Nicht angegeben';
        $summary = $subject;
        
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
        $name = $contactPerson . ' (' . $businessName . ')';
        $summary = $productType . ' - ' . $spaceType;
        
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
        $summary = "Vermutete Händler: $vendor1, $vendor2, $vendor3";
        
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
        break;
        
    case 'newsletter':
        $email = $data['email'] ?? '';
        $type = $data['type'] ?? 'customer';
        $name = $email; // Setze E-Mail als Name, da kein Name angegeben wird
        $summary = "Typ: " . ($type === 'vendor' ? 'Direktvermarkter' : 'Kunde');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.'
            ]);
            exit;
        }
        
        // E-Mail-Betreff und Inhalt erstellen
        $email_subject = "[housnkuh Newsletter] Neue Anmeldung";
        $email_body = "Neue Newsletter-Anmeldung:\n\n";
        $email_body .= "E-Mail: $email\n";
        $email_body .= "Typ: " . ($type === 'vendor' ? 'Direktvermarkter' : 'Kunde') . "\n";
        break;
        
    default:
        // Allgemeiner Fall
        $name = 'Unbekannt (Generic Form)';
        $email = isset($data['email']) ? $data['email'] : 'keine@angabe.de';
        $summary = 'Generisches Formular';
        
        $email_subject = "[housnkuh Website] Neue Formularanfrage";
        $email_body = "Neue Anfrage über die Website:\n\n";
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $email_body .= "$key: " . json_encode($value, JSON_UNESCAPED_UNICODE) . "\n";
            } else {
                $email_body .= "$key: $value\n";
            }
        }
}

// Versuchen, die Daten in der Datenbank zu speichern
try {
    // Konfigurationsdatei einbinden
    $hasConfig = @include_once(dirname(__FILE__) . '/config.php');
    
    if (!$hasConfig) {
        // Fallback-Konfiguration
        $dbConfig = [
            'host' => 'localhost',
            'port' => '3307',
            'dbname' => 'he56tye_housnkuh',
            'username' => 'yhe56tye_eva',
            'password' => 'SherlockHolmes2!'
        ];
    }
    
    // Datenbankverbindung herstellen
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset=utf8mb4";
    $pdo = new PDO(
        $dsn,
        $dbConfig['username'],
        $dbConfig['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Erstelle eine universelle Tabelle für alle Formulartypen, falls nicht existiert
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS form_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            form_type VARCHAR(50) NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50),
            summary TEXT,
            data TEXT,
            ip_address VARCHAR(45),
            status ENUM('new', 'contacted', 'processed', 'canceled') DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Serialisiere die gesamten Daten für die Speicherung
    $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
    
    // Speichere den Eintrag in der Datenbank
    $stmt = $pdo->prepare("
        INSERT INTO form_submissions 
        (form_type, name, email, phone, summary, data, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $form_type,
        $name,
        $email,
        $phone,
        $summary,
        $jsonData,
        $_SERVER['REMOTE_ADDR']
    ]);
    
    // Logging für erfolgreiche DB-Speicherung
    file_put_contents('form_submissions.log', "Daten erfolgreich in die Datenbank gespeichert (ID: {$pdo->lastInsertId()})\n", FILE_APPEND);
    
} catch (PDOException $e) {
    // Fehler protokollieren, aber fortfahren
    file_put_contents('form_submissions.log', "DB-Fehler beim Speichern: {$e->getMessage()}\n", FILE_APPEND);
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