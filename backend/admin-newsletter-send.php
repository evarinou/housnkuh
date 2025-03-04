<?php
// Admin-Interface zum Versenden von Newslettern
session_start();

// Direkten Datenbankeinstellungen
$host = '127.0.0.1';
$port = 3307;
$database = 'yhe56tye_housnkuh';
$username = 'yhe56tye_eva';
$password = 'SherlockHolmes2!'; // Ersetzen Sie dies mit Ihrem Passwort

// Admin-Passwort
$admin_password = 'admin123'; // Ändern Sie dies zu einem sicheren Passwort

// Laden der Konfigurationsdatei, falls vorhanden
$configFile = dirname(__FILE__) . '/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
    $admin_password = $adminConfig['password'] ?? $admin_password;


// Vorschau des Newsletters generieren
function previewNewsletter($subject, $content) {
    // Ersetze Platzhalter in der Vorschau
    $preview_content = personalizeNewsletter($content, 'preview@example.com', 'preview');
    
    $fullPreview = '
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Vorschau: ' . htmlspecialchars($subject) . '</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0; 
                padding: 0; 
            }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                padding: 20px; 
                border: 1px solid #ddd;
            }
            .header { 
                background-color: #09122c; 
                color: white; 
                padding: 20px; 
                text-align: center; 
            }
            .preview-banner {
                background-color: #e17564;
                color: white;
                text-align: center;
                padding: 10px;
                font-weight: bold;
            }
            .logo { 
                max-width: 150px; 
                height: auto; 
            }
            .content { 
                padding: 20px; 
                background-color: #fff; 
            }
            .footer { 
                text-align: center; 
                padding: 20px; 
                font-size: 12px; 
                color: #666; 
                background-color: #f5f5f5; 
            }
        </style>
    </head>
    <body>
        <div class="preview-banner">VORSCHAU - Wird nicht an Empfänger gesendet</div>
        <div class="container">
            <div class="header">
                <img src="/logo.svg" alt="housnkuh Logo" class="logo">
            </div>
            
            <div class="content">
                ' . $preview_content . '
            </div>
            
            <div class="footer">
                <p>Sie erhalten diesen Newsletter, weil Sie sich auf housnkuh.de angemeldet haben.</p>
                <p><a href="#">Newsletter abbestellen</a></p>
                <div class="social-links">
                    <a href="#">Instagram</a> | <a href="#">Facebook</a>
                </div>
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
    
    return $fullPreview;
}

// Vorschau anzeigen, wenn angefordert
$show_preview = false;
if (isset($_POST['preview_newsletter']) && isset($_SESSION['admin_authenticated'])) {
    $subject = $_POST['subject'] ?? 'Neuigkeiten von housnkuh';
    $content = $_POST['content'] ?? '';
    
    if (!empty($content)) {
        $show_preview = true;
        $preview_html = previewNewsletter($subject, $content);
    } else {
        $preview_error = 'Bitte geben Sie einen Inhalt für die Vorschau ein.';
    }
}

// Newsletter-Statistiken abrufen
function getNewsletterStats($pdo) {
    $stats = [];
    
    // Gesamtzahl der Abonnenten
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM newsletter WHERE confirmed = 1 AND active = 1");
    $stats['total_subscribers'] = $stmt->fetchColumn();
    
    // Anzahl nach Typ
    $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM newsletter WHERE confirmed = 1 AND active = 1 GROUP BY type");
    $stats['by_type'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Letzte Kampagnen
    try {
        $stmt = $pdo->query("
            SELECT campaign_id, subject, sent_date, sent_count, recipient_type 
            FROM newsletter_campaigns 
            ORDER BY sent_date DESC 
            LIMIT 5
        ");
        $stats['recent_campaigns'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Tabelle existiert möglicherweise noch nicht
        $stats['recent_campaigns'] = [];
    }
    
    return $stats;
}

// HTML-Vorlagenvorschläge
$template_suggestions = [
    [
        'name' => 'Eröffnung ankündigen',
        'content' => '<h2>Die Eröffnung naht!</h2>
<p>Liebe Freunde der regionalen Produkte,</p>
<p>wir freuen uns sehr, Ihnen mitteilen zu können, dass die Eröffnung von housnkuh in greifbare Nähe rückt. In wenigen Wochen öffnen wir unsere Türen für Sie!</p>
<p>Als Direktvermarkter-Marktplatz in der Kronacher Innenstadt werden wir Ihnen eine vielfältige Auswahl regionaler Produkte bieten:</p>
<ul>
  <li>Frische Lebensmittel von lokalen Erzeugern</li>
  <li>Handwerkskunst aus der Region</li>
  <li>Spezialitäten und Delikatessen</li>
</ul>
<p>Merken Sie sich den Termin vor: <strong>[DATUM EINFÜGEN]</strong></p>
<p>Wir freuen uns auf Ihren Besuch!</p>
<p>Herzliche Grüße,<br>Ihr housnkuh-Team</p>'
    ],
    [
        'name' => 'Neue Produkte vorstellen',
        'content' => '<h2>Neue regionale Schätze bei housnkuh!</h2>
<p>Liebe Freunde von housnkuh,</p>
<p>wir freuen uns, Ihnen unsere neuesten Produkte von lokalen Erzeugern vorzustellen:</p>
<div style="margin: 20px 0;">
  <h3 style="color: #e17564;">Direktvermarkter im Fokus: [NAME]</h3>
  <p>[KURZE BESCHREIBUNG DES ERZEUGERS]</p>
  <p>Folgende Produkte können Sie ab sofort bei uns entdecken:</p>
  <ul>
    <li>[PRODUKT 1]</li>
    <li>[PRODUKT 2]</li>
    <li>[PRODUKT 3]</li>
  </ul>
  <p><a href="https://housnkuh.de/vendors" class="button" style="background-color: #e17564; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">Mehr erfahren</a></p>
</div>
<p>Wir freuen uns auf Ihren Besuch!</p>
<p>Herzliche Grüße,<br>Ihr housnkuh-Team</p>'
    ],
    [
        'name' => 'Saisonale Angebote',
        'content' => '<h2>Saisonale Spezialitäten aus der Region</h2>
<p>Liebe housnkuh-Gemeinschaft,</p>
<p>der [JAHRESZEIT] ist da, und mit ihr kommen frische, saisonale Produkte aus unserer Region!</p>
<p>In dieser Saison empfehlen wir besonders:</p>
<ul>
  <li><strong>[PRODUKT 1]</strong> - von [ERZEUGER 1]</li>
  <li><strong>[PRODUKT 2]</strong> - von [ERZEUGER 2]</li>
  <li><strong>[PRODUKT 3]</strong> - von [ERZEUGER 3]</li>
</ul>
<p><strong>Tipp:</strong> [SAISONALER TIPP ODER REZEPTVORSCHLAG]</p>
<p><a href="https://housnkuh.de" class="button" style="background-color: #e17564; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">Jetzt entdecken</a></p>
<p>Wir freuen uns auf Ihren Besuch!</p>
<p>Herzliche Grüße,<br>Ihr housnkuh-Team</p>'
    ]
];

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter-Versand - housnkuh Admin</title>
    <style>
        body {
            font-family: 'Quicksand', Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            color: #333;
        }
        h1, h2, h3 {
            color: #09122c;
        }
        h1 {
            border-bottom: 2px solid #e17564;
            padding-bottom: 10px;
        }
        .container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .main-content {
            flex: 1;
            min-width: 300px;
        }
        .sidebar {
            width: 300px;
            flex-shrink: 0;
        }
        .card {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stats {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-box {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            flex: 1;
            min-width: 120px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        button, .button {
            background-color: #e17564;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
        }
        button:hover, .button:hover {
            background-color: #d16453;
        }
        .secondary-button {
            background-color: #09122c;
        }
        .secondary-button:hover {
            background-color: #0a1940;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
        }
        .success {
            color: #2ecc71;
            margin-bottom: 15px;
        }
        .template-suggestion {
            background-color: white;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .template-suggestion:hover {
            background-color: #f9f9f9;
            border-color: #e17564;
        }
        .preview-frame {
            width: 100%;
            height: 500px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
        }
        .campaign-list {
            list-style-type: none;
            padding: 0;
        }
        .campaign-list li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .campaign-list li:last-child {
            border-bottom: none;
        }
        .tab-container {
            margin-bottom: 20px;
        }
        .tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
        }
        .tab {
            padding: 10px 15px;
            background-color: #f5f5f5;
            border-radius: 4px 4px 0 0;
            cursor: pointer;
        }
        .tab.active {
            background-color: #e17564;
            color: white;
        }
        .tab-content {
            display: none;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 0 4px 4px 4px;
        }
        .tab-content.active {
            display: block;
        }
        .login-form {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h1>Newsletter-Versand</h1>
    
    <?php if (!isset($_SESSION['admin_authenticated'])): ?>
        <div class="login-form">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post">
                <div>
                    <label for="password">Admin-Passwort:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Anmelden</button>
            </form>
        </div>
    <?php else: ?>
        <?php 
            try {
                $pdo = connectToDatabase();
                $stats = getNewsletterStats($pdo);
            } catch (PDOException $e) {
                echo '<div class="error">Datenbankfehler: ' . $e->getMessage() . '</div>';
                $stats = ['total_subscribers' => 0, 'by_type' => [], 'recent_campaigns' => []];
            }
        ?>
        
        <div class="container">
            <div class="main-content">
                <?php if (isset($success_message)): ?>
                    <div class="success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if (isset($send_error)): ?>
                    <div class="error"><?php echo $send_error; ?></div>
                <?php endif; ?>
                
                <?php if ($show_preview): ?>
                    <h2>Newsletter-Vorschau</h2>
                    <div style="margin-bottom: 15px;">
                        <button onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'">
                            Zurück zum Editor
                        </button>
                    </div>
                    
                    <iframe class="preview-frame" srcdoc="<?php echo htmlspecialchars($preview_html); ?>"></iframe>
                <?php else: ?>
                    <div class="tab-container">
                        <div class="tabs">
                            <div class="tab active" onclick="switchTab(0)">Newsletter erstellen</div>
                            <div class="tab" onclick="switchTab(1)">Vorlagen</div>
                        </div>
                        
                        <div class="tab-content active">
                            <form method="post" action="">
                                <div>
                                    <label for="subject">Betreff:</label>
                                    <input type="text" id="subject" name="subject" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : 'Neuigkeiten von housnkuh'; ?>" required>
                                </div>
                                
                                <div>
                                    <label for="recipient_type">Empfänger:</label>
                                    <select id="recipient_type" name="recipient_type">
                                        <option value="all" <?php echo (isset($_POST['recipient_type']) && $_POST['recipient_type'] == 'all') ? 'selected' : ''; ?>>Alle Abonnenten</option>
                                        <option value="customer" <?php echo (isset($_POST['recipient_type']) && $_POST['recipient_type'] == 'customer') ? 'selected' : ''; ?>>Nur Kunden</option>
                                        <option value="vendor" <?php echo (isset($_POST['recipient_type']) && $_POST['recipient_type'] == 'vendor') ? 'selected' : ''; ?>>Nur Direktvermarkter</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="content">Inhalt (HTML):</label>
                                    <textarea id="content" name="content" rows="15" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                                </div>
                                
                                <div style="margin-top: 15px; display: flex; gap: 10px;">
                                    <button type="submit" name="preview_newsletter">Vorschau anzeigen</button>
                                    
                                    <div style="flex-grow: 1;"></div>
                                    
                                    <div style="background-color: #f5f5f5; padding: 10px; border-radius: 4px; display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox" id="test_mode" name="test_mode" value="1" <?php echo isset($_POST['test_mode']) ? 'checked' : ''; ?>>
                                        <label for="test_mode" style="display: inline; margin-bottom: 0;">Testmodus</label>
                                        <input type="email" id="test_email" name="test_email" placeholder="Test-Email" style="width: 200px; margin-bottom: 0;" value="<?php echo isset($_POST['test_email']) ? htmlspecialchars($_POST['test_email']) : ''; ?>">
                                    </div>
                                    
                                    <button type="submit" name="send_newsletter" class="secondary-button">Newsletter senden</button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="tab-content">
                            <h3>Vorlagenvorschläge</h3>
                            <p>Klicken Sie auf eine Vorlage, um sie in den Editor zu übernehmen:</p>
                            
                            <?php foreach ($template_suggestions as $template): ?>
                                <div class="template-suggestion" onclick="useTemplate('<?php echo addslashes($template['content']); ?>')">
                                    <strong><?php echo htmlspecialchars($template['name']); ?></strong>
                                    <p><?php echo substr(strip_tags($template['content']), 0, 100); ?>...</p>
                                </div>
                            <?php endforeach; ?>
                            
                            <p style="margin-top: 20px;">
                                <strong>Hinweis:</strong> Bitte passen Sie die Vorlagen entsprechend an und
                                ersetzen Sie die Platzhalter in [GROSSBUCHSTABEN] mit Ihren eigenen Inhalten.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="sidebar">
                <div class="card">
                    <h3>Newsletter-Statistik</h3>
                    <p><strong>Gesamt-Abonnenten:</strong> <?php echo $stats['total_subscribers']; ?></p>
                    
                    <h4>Nach Typ:</h4>
                    <ul>
                        <li><strong>Kunden:</strong> <?php echo $stats['by_type']['customer']

// Login-Verarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_authenticated'] = true;
    } else {
        $error = 'Falsches Passwort';
    }
}

// Logout-Verarbeitung
if (isset($_POST['logout'])) {
    unset($_SESSION['admin_authenticated']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Datenbankverbindung herstellen
function connectToDatabase() {
    global $host, $port, $database, $username, $password;
    
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$database", 
            $username, 
            $password
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Datenbankfehler: " . $e->getMessage());
    }
}

// Newsletter absenden
if (isset($_POST['send_newsletter']) && isset($_SESSION['admin_authenticated'])) {
    try {
        $pdo = connectToDatabase();
        
        $subject = $_POST['subject'] ?? 'Neuigkeiten von housnkuh';
        $content = $_POST['content'] ?? '';
        $recipient_type = $_POST['recipient_type'] ?? 'all';
        
        if (empty($content)) {
            $send_error = 'Bitte geben Sie einen Inhalt für den Newsletter ein.';
        } else {
            // SQL-Abfrage basierend auf dem Empfängertyp
            $sql = "SELECT email, type FROM newsletter WHERE confirmed = 1 AND active = 1";
            if ($recipient_type !== 'all') {
                $sql .= " AND type = :type";
            }
            
            $stmt = $pdo->prepare($sql);
            if ($recipient_type !== 'all') {
                $stmt->bindParam(':type', $recipient_type);
            }
            $stmt->execute();
            $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Testmodus aktiviert?
            $test_mode = isset($_POST['test_mode']) && $_POST['test_mode'] == '1';
            $test_email = $_POST['test_email'] ?? '';
            
            // Im Testmodus nur an die Test-E-Mail senden
            if ($test_mode) {
                if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
                    $send_error = 'Bitte geben Sie eine gültige Test-E-Mail an.';
                }
            } else {
                $send_error = $send_error ?? 'Keine Empfänger gefunden.';
            }
        }
    } catch (PDOException $e) {
        $send_error = "Datenbankfehler beim Senden: " . $e->getMessage();
    } catch (Exception $e) {
        $send_error = "Fehler beim Senden: " . $e->getMessage();
    }
}

/**
 * Personalisiert den Newsletter mit Tracking-Links und Anrede
 */
function personalizeNewsletter($content, $email, $campaign_id) {
    // Basis-URL der Website
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'housnkuh.de';
    $baseUrl = $protocol . $host;
    
    // Tracking-Pixel für Öffnungen hinzufügen
    $tracking_pixel = '<img src="' . $baseUrl . '/track.php?type=open&campaign=' . $campaign_id . '&email=' . urlencode($email) . '" width="1" height="1" alt="">';
    
    // Links mit Tracking versehen
    $content = preg_replace_callback(
        '/<a\s+(?:[^>]*?\s+)?href=["\']([^"\']+)["\']/',
        function($matches) use ($baseUrl, $campaign_id, $email) {
            $url = $matches[1];
            $tracking_url = $baseUrl . '/track.php?type=click&campaign=' . $campaign_id . '&email=' . urlencode($email) . '&url=' . urlencode($url);
            return str_replace('href="' . $url . '"', 'href="' . $tracking_url . '"', $matches[0]);
        },
        $content
    );
    
    // Tracking-Pixel am Ende des Inhalts hinzufügen
    $content .= $tracking_pixel;
    
    return $content;
}

/**
 * Sendet einen Newsletter an die angegebene E-Mail-Adresse
 */
function sendNewsletter($to, $subject, $content) {
    // Erstelle Header für HTML-E-Mail
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: newsletter@housnkuh.de\r\n";
    $headers .= "Reply-To: eva-maria.schaller@housnkuh.de\r\n";
    
    // Erstelle den vollständigen HTML-Newsletter
    $htmlMessage = '
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($subject) . '</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0; 
                padding: 0; 
            }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                padding: 20px; 
            }
            .header { 
                background-color: #09122c; 
                color: white; 
                padding: 20px; 
                text-align: center; 
            }
            .logo { 
                max-width: 150px; 
                height: auto; 
            }
            .content { 
                padding: 20px; 
                background-color: #fff; 
            }
            .footer { 
                text-align: center; 
                padding: 20px; 
                font-size: 12px; 
                color: #666; 
                background-color: #f5f5f5; 
            }
            .button {
                display: inline-block;
                background-color: #e17564;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 4px;
                font-weight: bold;
            }
            .social-links {
                padding: 10px 0;
            }
            .social-links a {
                display: inline-block;
                margin: 0 5px;
            }
            @media screen and (max-width: 600px) {
                .container {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="https://housnkuh.de/logo.svg" alt="housnkuh Logo" class="logo">
            </div>
            
            <div class="content">
                ' . $content . '
            </div>
            
            <div class="footer">
                <p>Sie erhalten diesen Newsletter, weil Sie sich auf housnkuh.de angemeldet haben.</p>
                <p><a href="https://housnkuh.de/unsubscribe.php?email=' . urlencode($to) . '">Newsletter abbestellen</a></p>
                <div class="social-links">
                    <a href="#">Instagram</a> | <a href="#">Facebook</a>
                </div>
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
    
    // Sende die E-Mail
    return mail($to, $subject, $htmlMessage, $headers);
} else {
                    $recipients = [['email' => $test_email, 'type' => 'test']];
                }
            }
            
            if (!isset($send_error) && !empty($recipients)) {
                // Newsletter-ID generieren für Tracking
                $campaign_id = uniqid('nl_', true);
                
                // Erfolgreiches Senden zählen
                $success_count = 0;
                $failed_emails = [];
                
                // Newsletter an jeden Empfänger senden
                foreach($recipients as $recipient) {
                    $email = $recipient['email'];
                    $type = $recipient['type'];
                    
                    // Jeden Newsletter personalisieren und mit Tracking-Links versehen
                    $personalized_content = personalizeNewsletter($content, $email, $campaign_id);
                    
                    // Newsletter versenden
                    if (sendNewsletter($email, $subject, $personalized_content)) {
                        $success_count++;
                        
                        // Im Nicht-Testmodus Versandzeitpunkt aktualisieren
                        if (!$test_mode) {
                            $update = $pdo->prepare("UPDATE newsletter SET last_email_sent = NOW() WHERE email = ?");
                            $update->execute([$email]);
                        }
                    } else {
                        $failed_emails[] = $email;
                    }
                }
                
                // Meldung über Ergebnis
                if ($test_mode) {
                    $success_message = "Test-Newsletter wurde an {$test_email} gesendet.";
                } else {
                    $success_message = "Newsletter wurde an {$success_count} von " . count($recipients) . " Empfängern gesendet.";
                    if (!empty($failed_emails)) {
                        $success_message .= " Fehlgeschlagen: " . implode(", ", array_slice($failed_emails, 0, 3));
                        if (count($failed_emails) > 3) {
                            $success_message .= " und " . (count($failed_emails) - 3) . " weitere";
                        }
                    }
                }
                
                // Speichern des Newsletters in der Datenbank für die Historie
                $stmt = $pdo->prepare("
                    INSERT INTO newsletter_campaigns 
                    (campaign_id, subject, content, recipient_type, sent_date, sent_count) 
                    VALUES (?, ?, ?, ?, NOW(), ?)
                ");
                
                try {
                    $stmt->execute([$campaign_id, $subject, $content, $recipient_type, $success_count]);
                } catch (PDOException $e) {
                    // Tabelle existiert möglicherweise noch nicht - erstellen
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS newsletter_campaigns (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            campaign_id VARCHAR(64) NOT NULL,
                            subject VARCHAR(255) NOT NULL,
                            content TEXT NOT NULL,
                            recipient_type VARCHAR(20) NOT NULL,
                            sent_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            sent_count INT DEFAULT 0
                        )
                    ");
                    
                    // Erneut versuchen
                    $stmt->execute([$campaign_id, $subject, $content, $recipient_type, $success_count]);
                }