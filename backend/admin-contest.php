<?php
// Direkte Datenbankeinstellungen
$host = '127.0.0.1';
$port = 3307;
$database = 'yhe56tye_housnkuh';
$username = 'yhe56tye_eva';
$password = 'SherlockHolmes2!'; // Ersetzen Sie dies mit Ihrem Passwort

// Admin-Passwort - sollte das gleiche wie in admin-newsletter.php sein
$admin_password = 'SherlockHolmes2!';

session_start();

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

// Gewinner-Auswahl
if (isset($_POST['draw_winner']) && isset($_SESSION['admin_authenticated'])) {
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$database", 
            $username, 
            $password
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Zufälligen Gewinner wählen
        $stmt = $pdo->query("SELECT id, name, email FROM vendor_contest ORDER BY RAND() LIMIT 1");
        $winner = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($winner) {
            $success_message = "Gewinner ausgelost: " . htmlspecialchars($winner['name']) . " (" . htmlspecialchars($winner['email']) . ")";
        } else {
            $error = "Keine Teilnehmer gefunden";
        }
    } catch(PDOException $e) {
        $error = "Datenbankfehler: " . $e->getMessage();
    }
}

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv' && isset($_SESSION['admin_authenticated'])) {
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$database", 
            $username, 
            $password
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SELECT * FROM vendor_contest ORDER BY submitted_at DESC");
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($entries) > 0) {
            // CSV Header
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=vendor-contest-entries.csv');
            
            // CSV Ausgabe
            $output = fopen('php://output', 'w');
            fputcsv($output, array_keys($entries[0]));
            
            foreach ($entries as $entry) {
                fputcsv($output, $entry);
            }
            
            fclose($output);
            exit;
        } else {
            $error = "Keine Daten zum Exportieren";
        }
    } catch(PDOException $e) {
        $error = "Datenbankfehler: " . $e->getMessage();
    }
}

// Löschen eines Eintrags
if (isset($_GET['delete']) && isset($_SESSION['admin_authenticated']) && is_numeric($_GET['delete'])) {
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$database", 
            $username, 
            $password
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("DELETE FROM vendor_contest WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $success_message = "Eintrag erfolgreich gelöscht.";
    } catch(PDOException $e) {
        $error = "Fehler beim Löschen: " . $e->getMessage();
    }
}

// Statistik zur Anzahl der gleichen Vermutungen
function getVendorStats($pdo) {
    try {
        // Top 10 vermutete Direktvermarkter für jede Position
        $positions = [];
        
        for ($i = 1; $i <= 3; $i++) {
            $stmt = $pdo->query("
                SELECT vendor$i as vendor, COUNT(*) as count 
                FROM vendor_contest 
                GROUP BY vendor$i 
                ORDER BY count DESC 
                LIMIT 10
            ");
            $positions[$i] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $positions;
    } catch(PDOException $e) {
        return null;
    }
}

// Alle Einträge abrufen wenn authentifiziert
if (isset($_SESSION['admin_authenticated'])) {
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$database", 
            $username, 
            $password
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Prüfen ob die Tabelle existiert
        $pdo->query("
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
        
        $stmt = $pdo->query("SELECT * FROM vendor_contest ORDER BY submitted_at DESC");
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_entries = count($entries);
        
        // Direktvermarkter-Statistik
        $vendor_stats = getVendorStats($pdo);
        
    } catch(PDOException $e) {
        $error = "Datenbankfehler: " . $e->getMessage();
        $entries = [];
        $total_entries = 0;
        $vendor_stats = null;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">