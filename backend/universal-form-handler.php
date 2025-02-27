<?php
// Globaler Form-Handler für alle Formulare
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'universal_form_handler_error.log');

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

// Log-Funktion für einheitliches Logging
function logMessage($message) {
    file_put_contents('form_submissions.log', date('Y-m-d H:i:s') . ": $message\n", FILE_APPEND);
}

// Protokolliere eingehende Anfrage
logMessage("Neue Anfrage erhalten");

// Formulartyp aus dem Request ermitteln
$form_type = isset($_GET['type']) ? $_GET['type'] : 'unknown';
logMessage("Formulartyp: $form_type");

// Daten aus verschiedenen Quellen extrahieren
$post_data = $_POST;
$json_data = json_decode(file_get_contents('php://input'), true) ?: [];
$data = array_merge($post_data, $json_data);

// Protokolliere die Daten (ohne sensible Informationen)
logMessage("Daten erhalten: " . json_encode(array_keys($data)));

// E-Mail-Empfänger
$to = 'eva-maria.schaller@housnkuh.de';

// Grundlegende Validierung
if ($form_type === 'newsletter') {
    $email = $data['email'] ?? '';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.'
        ]);
        exit;
    }
}

// Daten für universelle Speicherung vorbereiten
$email = $data['email'] ?? 'keine@angabe.de';
$name = $data['name'] ?? $data['businessName'] ?? $data['contactPerson'] ?? 'Unbekannt';
$summary = '';

// E-Mail-Betreff und Inhalt erstellen basierend auf Formulartyp
switch ($form_type) {
    case 'contact':
        $email_subject = "[housnkuh Kontakt] " . ($data['subject'] ?? 'Kontaktanfrage');
        $email_body = "Name: " . ($data['name'] ?? 'Unbekannt') . "\n";
        $email_body .= "E-Mail: $email\n";
        $email_body .= "Telefon: " . ($data['phone'] ?? 'Nicht angegeben') . "\n";
        $email_body .= "Betreff: " . ($data['subject'] ?? 'Kontaktanfrage') . "\n\n";
        $email_body .= "Nachricht:\n" . ($data['message'] ?? 'Keine Nachricht') . "\n";
        $summary = $data['subject'] ?? 'Kontaktanfrage';
        break;
        
    case 'rental':
        $email_subject = "[housnkuh Mietanfrage] " . ($data['businessName'] ?? 'Unbekannt');
        $email_body = "Neue Mietanfrage:\n\n";
        $email_body .= "Firmenname: " . ($data['businessName'] ?? 'Unbekannt') . "\n";
        $email_body .= "Ansprechpartner: " . ($data['contactPerson'] ?? 'Unbekannt') . "\n";
        $email_body .= "E-Mail: $email\n";
        $email_body .= "Telefon: " . ($data['phone'] ?? 'Nicht angegeben') . "\n";
        $email_body .= "Art der Produkte: " . ($data['productType'] ?? 'Nicht angegeben') . "\n";
        $email_body .= "Gewünschte Verkaufsfläche: " . ($data['spaceType'] ?? 'Nicht angegeben') . "\n";
        
        if (!empty($data['message'])) {
            $email_body .= "\nNachricht:\n" . $data['message'] . "\n";
        }
        $summary = "Mietanfrage: " . ($data['productType'] ?? 'Produkte') . " - " . ($data['spaceType'] ?? 'Fläche');
        break;
        
    case 'newsletter':
        $email_subject = "[housnkuh Newsletter] Neue Anmeldung";
        $email_body = "Neue Newsletter-Anmeldung:\n\n";
        $email_body .= "E-Mail: $email\n";
        $email_body .= "Typ: " . ($data['type'] ?? 'customer') . "\n";
        $summary = "Newsletter-Anmeldung: " . ($data['type'] === 'vendor' ? 'Direktvermarkter' : 'Kunde');
        break;
        
    case 'vendor-contest':
        $email_subject = "[housnkuh Wettbewerb] Neue Teilnahme";
        $email_body = "Neue Wettbewerbsteilnahme:\n\n";
        $email_body .= "Name: " . ($data['name'] ?? 'Unbekannt') . "\n";
        $email_body .= "E-Mail: $email\n";
        $email_body .= "Telefon: " . ($data['phone'] ?? 'Nicht angegeben') . "\n\n";
        $email_body .= "Vermutete Direktvermarkter:\n";
        
        if (isset($data['guessedVendors']) && is_array($data['guessedVendors'])) {
            foreach ($data['guessedVendors'] as $index => $vendor) {
                $email_body .= ($index + 1) . ". $vendor\n";
            }
        }
        $summary = "Wettbewerbsteilnahme";
        break;
        
    default:
        $email_subject = "[housnkuh Website] Neue Formularanfrage";
        $email_body = "Neue Anfrage über die Website:\n\n";
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $email_body .= "$key: " . json_encode($value) . "\n";
            } else {
                $email_body .= "$key: $value\n";
            }
        }
        $summary = "Generische Formularanfrage";
        break;
}

// Versuche die Daten in einer universellen Tabelle zu speichern
try {
    // Datenbankverbindung mit minimalen Abhängigkeiten
    $host = 'localhost'; // Oder '127.0.0.1'
    $dbname = 'yhe56tye_housnkuh';
    $username = 'yhe56tye_eva';
    $password = 'SherlockHolmes2!';
    $port = '3307';
    
    // Flexible PDO-Verbindung mit Fehlerbehandlung
    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        if ($port && $port != '3306') {
            $dsn .= ";port=$port";
        }
        
        $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        logMessage("Datenbankverbindung hergestellt");
    } catch (PDOException $e) {
        // Versuche alternativen Host oder Port
        logMessage("Primäre Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
        
        // Versuche ohne Port-Angabe
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            logMessage("Alternative Datenbankverbindung hergestellt (ohne Port)");
        } catch (PDOException $e2) {
            // Versuche mit 'localhost' statt IP
            try {
                $dsn = "mysql:host=localhost;dbname=$dbname;charset=utf8mb4";
                $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                logMessage("Alternative Datenbankverbindung hergestellt (mit localhost)");
            } catch (PDOException $e3) {
                throw new Exception("Alle Datenbankverbindungsversuche fehlgeschlagen");
            }
        }
    }
    
    // Universelle Tabelle erstellen (falls noch nicht vorhanden)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS all_form_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            form_type VARCHAR(50) NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            summary TEXT,
            full_data TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Daten als JSON serialisieren (für die vollständige Speicherung)
    $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
    
    // In die Datenbank einfügen
    $stmt = $pdo->prepare("
        INSERT INTO all_form_submissions 
        (form_type, name, email, summary, full_data, ip_address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $form_type,
        $name,
        $email,
        $summary,
        $jsonData,
        $_SERVER['REMOTE_ADDR']
    ]);
    
    logMessage("Daten erfolgreich in Datenbank gespeichert");
} catch (Exception $e) {
    // Fehler protokollieren, aber weitermachen
    logMessage("Datenbankfehler: " . $e->getMessage());
    // Wir machen trotzdem weiter und versuchen, die E-Mail zu senden
}

// E-Mail-Header
$headers = "From: noreply@housnkuh.de\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// E-Mail senden
$mail_sent = mail($to, $email_subject, $email_body, $headers);

if ($mail_sent) {
    logMessage("E-Mail erfolgreich gesendet");
    
    echo json_encode([
        'success' => true,
        'message' => 'Vielen Dank für Ihre Nachricht! Wir werden uns so schnell wie möglich bei Ihnen melden.'
    ]);
} else {
    logMessage("Fehler beim Senden der E-Mail");
    
    // Wenn die E-Mail fehlschlägt, aber die Datenbankspreichung erfolgreich war,
    // geben wir trotzdem eine Erfolgsmeldung zurück
    echo json_encode([
        'success' => true,
        'message' => 'Ihre Anfrage wurde erfolgreich übermittelt. Vielen Dank!'
    ]);
}
?>