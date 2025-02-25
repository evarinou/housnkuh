<?php
// Lade Konfiguration
require_once 'config.php';

// CORS-Header setzen
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Bei OPTIONS-Anfrage sofort beenden (für CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Daten aus der Anfrage lesen (unterstützt sowohl FormData als auch JSON)
$input = file_get_contents('php://input');
if (!empty($input)) {
    // JSON-Anfrage
    $data = json_decode($input, true);
    $email = isset($data['email']) ? $data['email'] : '';
    $type = isset($data['type']) ? $data['type'] : 'customer';
} else {
    // FormData-Anfrage
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $type = isset($_POST['type']) ? $_POST['type'] : 'customer';
}

// Einfache Validierung
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.']);
    exit;
}

// MySQL-Verbindung herstellen
try {
    $db = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}", 
        $dbConfig['username'], 
        $dbConfig['password']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prüfen, ob die E-Mail bereits existiert
    $stmt = $db->prepare("SELECT id FROM newsletter WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Diese E-Mail-Adresse ist bereits registriert.']);
        exit;
    }
    
    // E-Mail in die Datenbank einfügen
    $stmt = $db->prepare("INSERT INTO newsletter (email, type) VALUES (?, ?)");
    $success = $stmt->execute([$email, $type]);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Vielen Dank für Ihre Anmeldung!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.']);
    }
} catch (PDOException $e) {
    error_log('Newsletter error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>