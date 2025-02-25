<?php
// CORS-Header setzen
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// CORS Preflight-Anfragen beantworten
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Nur POST-Anfragen bearbeiten
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Nur POST-Anfragen erlaubt']);
    exit;
}

// Datenbankverbindung herstellen
$host = '127.0.0.1';
$port = 3307;
$database = 'yhe56tye_housnkuh';
$username = 'yhe56tye_eva';
$password = 'IHR_PASSWORT_HIER'; // Ersetzen Sie dies mit Ihrem echten Passwort

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log('Datenbankfehler: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Datenbankverbindungsfehler']);
    exit;
}

// Daten aus der Anfrage lesen
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Gültige E-Mail-Adresse erforderlich']);
    exit;
}

$email = $data['email'];
$type = isset($data['type']) && in_array($data['type'], ['customer', 'vendor']) ? $data['type'] : 'customer';

// In Datenbank speichern
try {
    // Prüfen, ob die E-Mail bereits existiert
    $stmt = $pdo->prepare("SELECT id FROM newsletter WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Diese E-Mail ist bereits registriert']);
        exit;
    }
    
    // Neue Anmeldung einfügen
    $stmt = $pdo->prepare("INSERT INTO newsletter (email, type) VALUES (?, ?)");
    $success = $stmt->execute([$email, $type]);
    
    if ($success) {
        http_response_code(201);
        echo json_encode(['message' => 'Newsletter-Anmeldung erfolgreich!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Fehler beim Speichern der Anmeldung']);
    }
} catch(PDOException $e) {
    error_log('SQL-Fehler: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Datenbankfehler']);
}
?>