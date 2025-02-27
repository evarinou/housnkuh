<?php
// Grundlegende Fehlerbehandlung für Debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Protokollierung für Fehlersuche
function logMessage($message) {
    file_put_contents('form_handler.log', date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
}

// CORS-Header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Protokolliere jeden Aufruf
logMessage("Handler wurde aufgerufen: " . $_SERVER['REQUEST_METHOD']);

// Bei OPTIONS-Anfrage sofort beenden (für CORS Preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Für alle anderen Methoden außer POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Nur POST-Anfragen sind erlaubt.'
    ]);
    exit;
}

// Formulartyp aus dem Request ermitteln
$form_type = isset($_GET['type']) ? $_GET['type'] : 'unknown';
logMessage("Formulartyp: $form_type");

// Daten aus dem POST-Request
$data = $_POST;
logMessage("Erhaltene POST-Daten: " . json_encode($data));

// E-Mail-Empfänger
$to = 'eva-maria.schaller@housnkuh.de';
$email = isset($data['email']) ? $data['email'] : 'keine-email@example.com';
$name = isset($data['name']) ? $data['name'] : (isset($data['businessName']) ? $data['businessName'] : 'Unbekannt');
$subject = "Housnkuh: Neue " . ucfirst($form_type) . " Anfrage";
$message = "Neue Anfrage vom Formular: $form_type\n\n";

// Nachricht zusammenstellen
foreach ($data as $key => $value) {
    if (is_array($value)) {
        $message .= "$key: " . json_encode($value) . "\n";
    } else {
        $message .= "$key: $value\n";
    }
}

// Datenbankverbindung - Basierend auf funktionierender Konfiguration aus der Diagnose
try {
    // Die funktionierende Konfiguration aus dem Diagnose-Test
    $host = '127.0.0.1';
    $port = '3307';
    $dbname = 'yhe56tye_housnkuh';
    $username = 'yhe56tye_eva';
    $password = 'SherlockHolmes2!';
    
    // DSN mit dem funktionierenden Port
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    // Datenbankverbindung herstellen
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    logMessage("Datenbankverbindung erfolgreich hergestellt");
    
    // Bestehende Tabellen prüfen
    $tables_query = $pdo->query("SHOW TABLES");
    $tables = $tables_query->fetchAll(PDO::FETCH_COLUMN);
    logMessage("Vorhandene Tabellen: " . implode(", ", $tables));
    
    // Form-Submissions-Tabelle erstellen oder nutzen
    if (!in_array('form_submissions', $tables)) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS form_submissions (
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
        logMessage("Tabelle form_submissions wurde erstellt");
    }
    
    // Daten als JSON serialisieren
    $json_data = json_encode($data, JSON_UNESCAPED_UNICODE);
    $summary = "Formular: $form_type - $name";
    
    // In die Datenbank einfügen
    $stmt = $pdo->prepare("
        INSERT INTO form_submissions 
        (form_type, name, email, summary, full_data, ip_address) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $form_type,
        $name,
        $email,
        $summary,
        $json_data,
        $_SERVER['REMOTE_ADDR']
    ]);
    
    if ($result) {
        $insert_id = $pdo->lastInsertId();
        logMessage("Daten erfolgreich in Datenbank gespeichert (ID: $insert_id)");
    } else {
        logMessage("Fehler beim Speichern in der Datenbank: " . implode(", ", $stmt->errorInfo()));
    }
    
} catch (PDOException $e) {
    logMessage("Datenbankfehler: " . $e->getMessage());
    // Trotz Datenbankfehler weitermachen und E-Mail senden
}

// E-Mail senden
$headers = "From: webform@housnkuh.de\r\n";
$headers .= "Reply-To: $email\r\n";

$mail_sent = mail($to, $subject, $message, $headers);

if ($mail_sent) {
    logMessage("E-Mail erfolgreich gesendet");
    
    // Erfolgsantwort senden (unabhängig davon, ob die Datenbankverbindung funktioniert hat)
    echo json_encode([
        'success' => true,
        'message' => 'Vielen Dank für Ihre Nachricht! Wir werden uns so schnell wie möglich bei Ihnen melden.'
    ]);
} else {
    logMessage("Fehler beim Senden der E-Mail");
    
    // Fehlerantwort senden
    echo json_encode([
        'success' => false,
        'message' => 'Bei der Übermittlung ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.'
    ]);
}
?>