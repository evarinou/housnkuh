<?php
// Aktiviere Fehlerprotokollierung (in Produktion ggf. entfernen oder anpassen)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Protokolliere Fehler in eine Datei
ini_set('log_errors', 1);
ini_set('error_log', 'newsletter_error.log');

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
$logMessage = "Anfrage empfangen: " . date('Y-m-d H:i:s') . "\n";
$logMessage .= "Methode: " . $_SERVER['REQUEST_METHOD'] . "\n";
file_put_contents('newsletter_requests.log', $logMessage, FILE_APPEND);

// Daten aus der Anfrage lesen
$rawData = file_get_contents('php://input');
$contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

// Protokolliere den Inhalt der Anfrage
file_put_contents('newsletter_requests.log', "Content-Type: $contentType\n", FILE_APPEND);
file_put_contents('newsletter_requests.log', "Raw data: $rawData\n", FILE_APPEND);
file_put_contents('newsletter_requests.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

try {
    // Konfigurationsdatei einbinden (falls vorhanden)
    $configFile = dirname(__FILE__) . '/config.php';
    if (file_exists($configFile)) {
        require_once $configFile;
    } else {
        // Fallback Datenbankeinstellungen
        $dbConfig = [
            'host' => '127.0.0.1',
            'port' => '3307',
            'dbname' => 'yhe56tye_housnkuh',
            'username' => 'yhe56tye_eva',
            'password' => 'SherlockHolmes2!'
        ];
    }

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
        $db = new PDO(
            "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset=utf8mb4", 
            $dbConfig['username'], 
            $dbConfig['password'], 
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Tabelle erstellen falls nicht vorhanden
        $db->exec("
            CREATE TABLE IF NOT EXISTS newsletter (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                type ENUM('customer', 'vendor') NOT NULL,
                subscribedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                active BOOLEAN DEFAULT TRUE,
                confirmed BOOLEAN DEFAULT FALSE,
                confirmation_token VARCHAR(64) DEFAULT NULL,
                confirmation_date TIMESTAMP NULL DEFAULT NULL,
                last_email_sent TIMESTAMP NULL DEFAULT NULL,
                open_count INT DEFAULT 0,
                click_count INT DEFAULT 0
            )
        ");
        
        // Prüfen, ob die E-Mail bereits existiert
        $stmt = $db->prepare("SELECT id, confirmed FROM newsletter WHERE email = ?");
        $stmt->execute([$email]);
        $existingSubscription = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingSubscription) {
            // Falls bereits angemeldet, senden wir eine passende Rückmeldung
            if ($existingSubscription['confirmed']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Sie sind bereits für den Newsletter angemeldet.'
                ]);
            } else {
                // Bestätigungstoken neu generieren und E-Mail erneut senden
                $token = generateToken();
                
                $updateStmt = $db->prepare("
                    UPDATE newsletter 
                    SET confirmation_token = ?, 
                        subscribedAt = CURRENT_TIMESTAMP 
                    WHERE id = ?
                ");
                $updateStmt->execute([$token, $existingSubscription['id']]);
                
                // Bestätigungsmail senden
                if (sendConfirmationEmail($email, $token)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Bitte bestätigen Sie Ihre E-Mail-Adresse. Wir haben Ihnen einen Bestätigungslink zugeschickt.'
                    ]);
                } else {
                    throw new Exception('E-Mail konnte nicht gesendet werden.');
                }
            }
            exit;
        }
        
        // Bestätigungstoken generieren
        $token = generateToken();
        
        // E-Mail in die Datenbank einfügen
        $stmt = $db->prepare("
            INSERT INTO newsletter 
            (email, type, confirmation_token) 
            VALUES (?, ?, ?)
        ");
        $success = $stmt->execute([$email, $type, $token]);
        
        if ($success) {
            // Bestätigungsmail senden
            if (sendConfirmationEmail($email, $token)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Vielen Dank für Ihre Anmeldung! Bitte bestätigen Sie Ihre E-Mail-Adresse.'
                ]);
                
                // Protokolliere den Erfolg
                file_put_contents('newsletter_requests.log', "Erfolgreiche Anmeldung für: $email\n", FILE_APPEND);
            } else {
                throw new Exception('E-Mail konnte nicht gesendet werden.');
            }
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

/**
 * Generiert ein zufälliges Token für die E-Mail-Bestätigung
 */
function generateToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Sendet eine Bestätigungs-E-Mail mit dem Bestätigungslink
 */
function sendConfirmationEmail($email, $token) {
    // Website-URL aus dem Server-Request ableiten oder fest definieren
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'housnkuh.de';
    $baseUrl = $protocol . $host;
    
    // Bestätigungslink erstellen
    $confirmationLink = "{$baseUrl}/newsletter-confirm.php?token={$token}&email=" . urlencode($email);
    
    // E-Mail-Header
    $to = $email;
    $subject = 'Bitte bestätigen Sie Ihre Newsletter-Anmeldung - housnkuh';
    $headers = "From: newsletter@housnkuh.de\r\n";
    $headers .= "Reply-To: eva-maria.schaller@housnkuh.de\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // HTML-E-Mail-Inhalt
    $message = '
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <style>
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                max-width: 600px; 
                margin: 0 auto; 
            }
            .container { 
                padding: 20px; 
                background-color: #f9f9f9; 
            }
            .header { 
                text-align: center; 
                padding-bottom: 20px; 
                border-bottom: 2px solid #e17564; 
                margin-bottom: 20px; 
            }
            .logo { 
                max-width: 150px; 
                height: auto; 
            }
            h1 { 
                color: #09122c; 
                margin-bottom: 20px; 
            }
            .content { 
                margin-bottom: 30px; 
            }
            .button { 
                display: inline-block; 
                background-color: #e17564; 
                color: white; 
                padding: 12px 24px; 
                text-decoration: none; 
                border-radius: 4px; 
                font-weight: bold; 
            }
            .button-container { 
                text-align: center; 
                margin: 30px 0; 
            }
            .footer { 
                font-size: 12px; 
                color: #666; 
                border-top: 1px solid #ddd; 
                padding-top: 20px; 
                margin-top: 30px; 
            }
            .confirmation-link {
                word-break: break-all;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="' . $baseUrl . '/logo.svg" alt="housnkuh Logo" class="logo">
                <h1>Bitte bestätigen Sie Ihre Newsletter-Anmeldung</h1>
            </div>
            
            <div class="content">
                <p>Vielen Dank für Ihre Anmeldung zum housnkuh-Newsletter!</p>
                
                <p>Um die Anmeldung abzuschließen und künftig Neuigkeiten über housnkuh zu erhalten, klicken Sie bitte auf den folgenden Button:</p>
                
                <div class="button-container">
                    <a href="' . $confirmationLink . '" class="button">Anmeldung bestätigen</a>
                </div>
                
                <p>Falls der Button nicht funktioniert, kopieren Sie bitte den folgenden Link in die Adresszeile Ihres Browsers:</p>
                
                <p class="confirmation-link">' . $confirmationLink . '</p>
                
                <p>Der Link ist 48 Stunden gültig.</p>
            </div>
            
            <div class="footer">
                <p>
                    Sie erhalten diese E-Mail, weil Sie sich für den housnkuh-Newsletter angemeldet haben.<br>
                    Falls Sie diese E-Mail irrtümlich erhalten haben, können Sie sie ignorieren.
                </p>
                <p>
                    housnkuh<br>
                    Strauer Str. 15<br>
                    96317 Kronach<br>
                    <a href="mailto:eva-maria.schaller@housnkuh.de">eva-maria.schaller@housnkuh.de</a>
                </p>
            </div>
        </div>
    </body>
    </html>';
    
    // E-Mail senden
    return mail($to, $subject, $message, $headers);
}