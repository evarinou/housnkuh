<?php
// Direkte Datenbankeinstellungen (alternativ config.php verwenden)
$host = '127.0.0.1';
$port = 3307;
$database = 'yhe56tye_housnkuh';
$username = 'yhe56tye_eva';
$password = 'SherlockHolmes2!'; // Ersetzen Sie dies mit Ihrem Passwort

// Admin-Passwort
$admin_password = 'admin123'; // Ändern Sie dies zu einem sicheren Passwort

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

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv' && isset($_SESSION['admin_authenticated'])) {
    $pdo = connectToDatabase();
    $stmt = $pdo->query("SELECT * FROM rental_requests ORDER BY created_at DESC");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // CSV Header
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=rental-requests.csv');
    
    // CSV Ausgabe
    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($entries[0]));
    
    foreach ($entries as $entry) {
        fputcsv($output, $entry);
    }
    
    fclose($output);
    exit;
}

// Status-Update einer Anfrage
if (isset($_GET['id']) && isset($_GET['status']) && isset($_SESSION['admin_authenticated'])) {
    $pdo = connectToDatabase();
    try {
        $validStatuses = ['new', 'contacted', 'approved', 'rejected'];
        
        if (in_array($_GET['status'], $validStatuses)) {
            $stmt = $pdo->prepare("UPDATE rental_requests SET status = ? WHERE id = ?");
            $stmt->execute([$_GET['status'], $_GET['id']]);
            $success_message = "Status erfolgreich aktualisiert.";
        } else {
            $error = "Ungültiger Status.";
        }
    } catch(PDOException $e) {
        $error = "Fehler beim Aktualisieren: " . $e->getMessage();
    }
}

// Löschen eines Eintrags
if (isset($_GET['delete']) && isset($_SESSION['admin_authenticated']) && is_numeric($_GET['delete'])) {
    $pdo = connectToDatabase();
    try {
        $stmt = $pdo->prepare("DELETE FROM rental_requests WHERE id = ?");
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
            CREATE TABLE IF NOT EXISTS rental_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                business_name VARCHAR(255) NOT NULL,
                contact_person VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                product_type VARCHAR(255) NOT NULL,
                space_type VARCHAR(50) NOT NULL,
                message TEXT,
                status ENUM('new', 'contacted', 'approved', 'rejected') DEFAULT 'new',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    } catch(PDOException $e) {
        $error = "Fehler beim Erstellen der Tabelle: " . $e->getMessage();
    }
}

// Filter-Einstellungen
$statusFilter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Alle Einträge abrufen wenn authentifiziert
if (isset($_SESSION['admin_authenticated'])) {
    $pdo = connectToDatabase();
    
    try {
        // SQL-Abfrage mit Filter
        $sql = "SELECT * FROM rental_requests";
        if ($statusFilter !== 'all') {
            $sql .= " WHERE status = :status";
        }
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        
        if ($statusFilter !== 'all') {
            $stmt->bindParam(':status', $statusFilter);
        }
        
        $stmt->execute();
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Statistiken
        $total_entries = $pdo->query("SELECT COUNT(*) FROM rental_requests")->fetchColumn();
        $new_entries = $pdo->query("SELECT COUNT(*) FROM rental_requests WHERE status = 'new'")->fetchColumn();
        $contacted_entries = $pdo->query("SELECT COUNT(*) FROM rental_requests WHERE status = 'contacted'")->fetchColumn();
        $approved_entries = $pdo->query("SELECT COUNT(*) FROM rental_requests WHERE status = 'approved'")->fetchColumn();
        $rejected_entries = $pdo->query("SELECT COUNT(*) FROM rental_requests WHERE status = 'rejected'")->fetchColumn();
    } catch(PDOException $e) {
        $error = "Datenbankfehler: " . $e->getMessage();
        $entries = [];
        $total_entries = 0;
        $new_entries = 0;
        $contacted_entries = 0;
        $approved_entries = 0;
        $rejected_entries = 0;
    }
}

// Mapping der Verkaufsflächen-IDs zu lesbaren Namen
$spaceTypeLabels = [
    'regal-a' => 'Verkaufsblock Lage A (35€/Monat)',
    'regal-b' => 'Verkaufsblock Lage B (15€/Monat)',
    'kuehl' => 'Verkaufsblock gekühlt (50€/Monat)',
    'tisch' => 'Verkaufsblock Tisch (40€/Monat)',
    'service' => 'Service-Paket Plus'
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mietanfragen-Verwaltung</title>
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
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
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
        .button-small {
            padding: 5px 10px;
            font-size: 0.9em;
        }
        .button-outline {
            background-color: transparent;
            border: 1px solid #e17564;
            color: #e17564;
        }
        .button-outline:hover {
            background-color: #e17564;
            color: white;
        }
        .button-outline.active {
            background-color: #e17564;
            color: white;
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
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            color: white;
        }
        .status-new {
            background-color: #3498db;
        }
        .status-contacted {
            background-color: #f39c12;
        }
        .status-approved {
            background-color: #2ecc71;
        }
        .status-rejected {
            background-color: #e74c3c;
        }
        .message-preview {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .message-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            align-items: center;
        }
        .close-button {
            background: transparent;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .stats {
                flex-direction: column;
            }
            .actions {
                flex-direction: column;
            }
            .filters {
                flex-wrap: wrap;
            }
            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <h1>Mietanfragen-Verwaltung</h1>
    
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
               Datenbank: <?php echo $database; ?> | 
               Host: <?php echo $host; ?> | 
               Port: <?php echo $port; ?> | 
               Benutzer: <?php echo $username; ?>
            </p>
        </div>
        
        <div class="stats">
            <div class="stat-box">
                <h3>Gesamt</h3>
                <p><?php echo $total_entries; ?> Anfragen</p>
            </div>
            <div class="stat-box">
                <h3>Neu</h3>
                <p><?php echo $new_entries; ?> Anfragen</p>
            </div>
            <div class="stat-box">
                <h3>Kontaktiert</h3>
                <p><?php echo $contacted_entries; ?> Anfragen</p>
            </div>
            <div class="stat-box">
                <h3>Genehmigt</h3>
                <p><?php echo $approved_entries; ?> Anfragen</p>
            </div>
            <div class="stat-box">
                <h3>Abgelehnt</h3>
                <p><?php echo $rejected_entries; ?> Anfragen</p>
            </div>
        </div>
        
        <div class="filters">
            <strong>Status-Filter:</strong>
            <a href="?filter=all" class="button button-small button-outline <?php echo $statusFilter === 'all' ? 'active' : ''; ?>">Alle</a>
            <a href="?filter=new" class="button button-small button-outline <?php echo $statusFilter === 'new' ? 'active' : ''; ?>">Neu</a>
            <a href="?filter=contacted" class="button button-small button-outline <?php echo $statusFilter === 'contacted' ? 'active' : ''; ?>">Kontaktiert</a>
            <a href="?filter=approved" class="button button-small button-outline <?php echo $statusFilter === 'approved' ? 'active' : ''; ?>">Genehmigt</a>
            <a href="?filter=rejected" class="button button-small button-outline <?php echo $statusFilter === 'rejected' ? 'active' : ''; ?>">Abgelehnt</a>
        </div>
        
        <div class="actions">
            <a href="?export=csv" class="button">Als CSV exportieren</a>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="logout" value="1">
                <button type="submit" class="button" style="background-color: #666;">Abmelden</button>
            </form>
        </div>
        
        <?php if ($total_entries > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Firma</th>
                            <th>Kontakt</th>
                            <th>E-Mail / Telefon</th>
                            <th>Produkte</th>
                            <th>Verkaufsfläche</th>
                            <th>Nachricht</th>
                            <th>Status</th>
                            <th>Datum</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td><?php echo $entry['id']; ?></td>
                                <td><?php echo htmlspecialchars($entry['business_name']); ?></td>
                                <td><?php echo htmlspecialchars($entry['contact_person']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($entry['email']); ?>
                                    <?php if (!empty($entry['phone'])): ?>
                                        <br><?php echo htmlspecialchars($entry['phone']); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($entry['product_type']); ?></td>
                                <td>
                                    <?php 
                                        $spaceType = $entry['space_type'];
                                        echo isset($spaceTypeLabels[$spaceType]) ? $spaceTypeLabels[$spaceType] : $spaceType;
                                    ?>
                                </td>
                                <td>
                                    <?php if (!empty($entry['message'])): ?>
                                        <div class="message-preview" onclick="showMessage('<?php echo $entry['id']; ?>')"><?php echo htmlspecialchars($entry['message']); ?></div>
                                    <?php else: ?>
                                        <em>Keine Nachricht</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $statusClass = 'status-' . $entry['status'];
                                        $statusText = '';
                                        switch ($entry['status']) {
                                            case 'new': $statusText = 'Neu'; break;
                                            case 'contacted': $statusText = 'Kontaktiert'; break;
                                            case 'approved': $statusText = 'Genehmigt'; break;
                                            case 'rejected': $statusText = 'Abgelehnt'; break;
                                            default: $statusText = $entry['status'];
                                        }
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                                <td><?php echo date('d.m.Y H:i', strtotime($entry['created_at'])); ?></td>
                                <td>
                                    <div class="actions" style="margin-top: 0;">
                                        <div class="button-group">
                                            <a href="?id=<?php echo $entry['id']; ?>&status=contacted" class="button button-small <?php echo $entry['status'] === 'contacted' ? 'button-outline' : ''; ?>">Kontaktiert</a>
                                            <a href="?id=<?php echo $entry['id']; ?>&status=approved" class="button button-small <?php echo $entry['status'] === 'approved' ? 'button-outline' : ''; ?>" style="background-color: #2ecc71;">Genehmigt</a>
                                            <a href="?id=<?php echo $entry['id']; ?>&status=rejected" class="button button-small <?php echo $entry['status'] === 'rejected' ? 'button-outline' : ''; ?>" style="background-color: #e74c3c;">Abgelehnt</a>
                                            <a href="?delete=<?php echo $entry['id']; ?>" onclick="return confirm('Diesen Eintrag wirklich löschen?')" class="button button-small" style="background-color: #666;">Löschen</a>
                                            <?php if (!empty($entry['email'])): ?>
                                                <a href="mailto:<?php echo htmlspecialchars($entry['email']); ?>" class="button button-small" style="background-color: #3498db;">Mail</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <!-- Erstelle ein Modal für jede Nachricht -->
                            <?php if (!empty($entry['message'])): ?>
                                <div id="message-modal-<?php echo $entry['id']; ?>" class="message-modal">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3>Nachricht von <?php echo htmlspecialchars($entry['contact_person']); ?></h3>
                                            <button class="close-button" onclick="hideMessage('<?php echo $entry['id']; ?>')">&times;</button>
                                        </div>
                                        <p><strong>Firma:</strong> <?php echo htmlspecialchars($entry['business_name']); ?></p>
                                        <p><strong>Produkte:</strong> <?php echo htmlspecialchars($entry['product_type']); ?></p>
                                        <p><strong>Verkaufsfläche:</strong> <?php echo isset($spaceTypeLabels[$entry['space_type']]) ? $spaceTypeLabels[$entry['space_type']] : $entry['space_type']; ?></p>
                                        <p><strong>Nachricht:</strong></p>
                                        <div style="white-space: pre-wrap; background-color: #f5f5f5; padding: 10px; border-radius: 4px;">
                                            <?php echo htmlspecialchars($entry['message']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Keine Mietanfragen vorhanden.</p>
        <?php endif; ?>
    <?php endif; ?>

    <script>
        // Modal anzeigen
        function showMessage(id) {
            document.getElementById('message-modal-' + id).style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Verhindert Scrollen im Hintergrund
        }
        
        // Modal ausblenden
        function hideMessage(id) {
            document.getElementById('message-modal-' + id).style.display = 'none';
            document.body.style.overflow = ''; // Scrollen wieder erlauben
        }
        
        // Modals auch schließen, wenn außerhalb geklickt wird
        window.onclick = function(event) {
            if (event.target.classList.contains('message-modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = '';
            }
        }
    </script>
</body>
</html>