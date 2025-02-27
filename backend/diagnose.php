<?php
// Einfache Diagnose für PHP und Datenbank
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>PHP Diagnose für housnkuh</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Prüfe, ob die Universal Form Handler Datei existiert
echo "<h2>Dateiprüfung</h2>";
$files_to_check = [
    "universal-form-handler.php", 
    "newsletter.php",
    "form-handler.php",
    "contact-form.php",
    "rental-request.php"
];

foreach ($files_to_check as $file) {
    echo "<p>$file: " . (file_exists($file) ? "✅ Existiert" : "❌ Fehlt") . "</p>";
}

// Datenbankverbindung testen
echo "<h2>Datenbankverbindung</h2>";
try {
    // Versuche verschiedene Verbindungskonfigurationen
    $configs = [
        [
            "host" => "localhost", 
            "port" => "3306", 
            "dbname" => "yhe56tye_housnkuh",
            "user" => "yhe56tye_eva",
            "pass" => "SherlockHolmes2!"
        ],
        [
            "host" => "127.0.0.1", 
            "port" => "3307", 
            "dbname" => "yhe56tye_housnkuh",
            "user" => "yhe56tye_eva",
            "pass" => "SherlockHolmes2!"
        ],
        [
            "host" => "localhost", 
            "port" => "", 
            "dbname" => "yhe56tye_housnkuh",
            "user" => "yhe56tye_eva",
            "pass" => "SherlockHolmes2!"
        ]
    ];
    
    $connection_success = false;
    
    foreach ($configs as $index => $config) {
        echo "<p>Versuch " . ($index + 1) . ": ";
        
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
        if (!empty($config['port'])) {
            $dsn .= ";port={$config['port']}";
        }
        
        try {
            $pdo = new PDO($dsn, $config['user'], $config['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "✅ Erfolgreich!";
            $connection_success = true;
            
            // Tabellen auflisten
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<p>Gefundene Tabellen: ";
            if (count($tables) > 0) {
                echo implode(", ", $tables);
            } else {
                echo "Keine Tabellen gefunden";
            }
            echo "</p>";
            
            break;
        } catch (PDOException $e) {
            echo "❌ Fehlgeschlagen: " . $e->getMessage() . "</p>";
        }
    }
    
    if (!$connection_success) {
        echo "<p>Alle Datenbankverbindungsversuche sind fehlgeschlagen.</p>";
    }
} catch (Exception $e) {
    echo "<p>Fehler: " . $e->getMessage() . "</p>";
}

// Testen, ob mail() funktioniert
echo "<h2>E-Mail-Funktion</h2>";
$mail_result = mail("evam.schaller@gmail.com", "Diagnose Test", "Dies ist eine Test-E-Mail von der housnkuh Diagnose.");
echo "<p>mail() Funktion: " . ($mail_result ? "✅ Scheint zu funktionieren" : "❌ Fehlgeschlagen") . "</p>";

echo "<h2>Server-Informationen</h2>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>";
?>