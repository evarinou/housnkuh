<?php
// Lade Konfiguration
require_once 'config.php';

session_start();

// Login-Verarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $adminConfig['password']) {
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
    global $dbConfig;
    
    try {
        $pdo = new PDO(
            "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']}", 
            $dbConfig['username'], 
            $dbConfig['password']
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Datenbankfehler: " . $e->getMessage());
    }
}

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv' && isset($_SESSION['admin_authenticated'])) {
    $pdo = connectToDatabase();
    $stmt = $pdo->query("SELECT * FROM newsletter ORDER BY subscribedAt DESC");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // CSV Header
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=newsletter-subscribers.csv');
    
    // CSV Ausgabe
    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($entries[0]));
    
    foreach ($entries as $entry) {
        fputcsv($output, $entry);
    }
    
    fclose($output);
    exit;
}

// Löschen eines Eintrags
if (isset($_GET['delete']) && isset($_SESSION['admin_authenticated']) && is_numeric($_GET['delete'])) {
    $pdo = connectToDatabase();
    try {
        $stmt = $pdo->prepare("DELETE FROM newsletter WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $success_message = "Eintrag erfolgreich gelöscht.";
    } catch(PDOException $e) {
        $error = "Fehler beim Löschen: " . $e->getMessage();
    }
}

// Tabelle erstellen, falls nicht vorhanden
if (isset($_SESSION['admin_authenticated'])) {
    $pdo = connectToDatabase();
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS newsletter (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                type ENUM('customer', 'vendor') NOT NULL,
                subscribedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                active BOOLEAN DEFAULT TRUE
            )
        ");
    } catch(PDOException $e) {
        $error = "Fehler beim Erstellen der Tabelle: " . $e->getMessage();
    }
}

// Alle Einträge abrufen wenn authentifiziert
if (isset($_SESSION['admin_authenticated'])) {
    $pdo = connectToDatabase();
    
    // Prüfen ob die Tabelle existiert
    try {
        $stmt = $pdo->query("SELECT * FROM newsletter ORDER BY subscribedAt DESC");
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_entries = count($entries);
        
        $customers = $pdo->query("SELECT COUNT(*) FROM newsletter WHERE type = 'customer'")->fetchColumn();
        $vendors = $pdo->query("SELECT COUNT(*) FROM newsletter WHERE type = 'vendor'")->fetchColumn();
    } catch(PDOException $e) {
        $error = "Datenbankfehler: " . $e->getMessage();
        $entries = [];
        $total_entries = 0;
        $customers = 0;
        $vendors = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #09122c;
            border-bottom: 2px solid #e17564;
            padding-bottom: 10px;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #09122c;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            background-color: #e17564;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #09122c;
        }
        .login-form {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
        .info {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Newsletter-Verwaltung</h1>
    
    <?php if (!isset($_SESSION['admin_authenticated'])): ?>
        <div class="login-form">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="password">Admin-Passwort:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="button">Anmelden</button>
            </form>
        </div>
    <?php else: ?>
        <?php if (isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="info">
            <p><strong>Konfiguration:</strong> 
               Datenbank: <?php echo $dbConfig['dbname']; ?> | 
               Host: <?php echo $dbConfig['host']; ?> | 
               Port: <?php echo $dbConfig['port']; ?> | 
               Benutzer: <?php echo $dbConfig['username']; ?>
            </p>
        </div>
        
        <div class="stats">
            <div class="stat-box">
                <h3>Gesamtanzahl</h3>
                <p><?php echo $total_entries; ?> Anmeldungen</p>
            </div>
            <div class="stat-box">
                <h3>Kunden</h3>
                <p><?php echo $customers; ?> Anmeldungen</p>
            </div>
            <div class="stat-box">
                <h3>Direktvermarkter</h3>
                <p><?php echo $vendors; ?> Anmeldungen</p>
            </div>
        </div>
        
        <div class="actions">
            <a href="?export=csv" class="button">Als CSV exportieren</a>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="logout" value="1">
                <button type="submit" class="button" style="background-color: #666;">Abmelden</button>
            </form>
        </div>
        
        <?php if ($total_entries > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>E-Mail</th>
                        <th>Typ</th>
                        <th>Anmeldedatum</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td><?php echo $entry['id']; ?></td>
                            <td><?php echo htmlspecialchars($entry['email']); ?></td>
                            <td><?php echo $entry['type'] === 'customer' ? 'Kunde' : 'Direktvermarkter'; ?></td>
                            <td><?php echo $entry['subscribedAt']; ?></td>
                            <td>
                                <a href="?delete=<?php echo $entry['id']; ?>" onclick="return confirm('Diesen Eintrag wirklich löschen?')" class="button" style="background-color: #ff4444;">Löschen</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Keine Newsletter-Anmeldungen vorhanden.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>