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

// E-Mail senden
$headers = "From: webform@housnkuh.de\r\n";
$headers .= "Reply-To: $email\r\n";

$mail_sent = mail($to, $subject, $message, $headers);

if ($mail_sent) {
    logMessage("E-Mail erfolgreich gesendet");
    
    // Erfolgsantwort senden
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