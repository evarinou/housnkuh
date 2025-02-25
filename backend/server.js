// backend/server.js mit MySQL
const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
const dotenv = require('dotenv');

// Prüfen, welche Umgebung verwendet wird
if (process.env.NODE_ENV === 'development') {
    require('dotenv').config({ path: '.env.development' });
  } else {
    require('dotenv').config();
  }

// MySQL Connection Pool
const pool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

// Initialize express
const app = express();

// Middleware
app.use(cors({
  origin: ['http://localhost:3000', 'https://housnkuh.de'],
  methods: ['GET', 'POST'],
  credentials: true
}));
app.use(express.json());

// Datenbank-Setup
async function initializeDatabase() {
  try {
    const connection = await pool.getConnection();
    console.log('MySQL erfolgreich verbunden');
    
    // Newsletter-Tabelle erstellen
    await connection.execute(`
      CREATE TABLE IF NOT EXISTS newsletter (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        type ENUM('customer', 'vendor') NOT NULL,
        subscribedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        active BOOLEAN DEFAULT TRUE
      )
    `);
    
    connection.release();
    console.log('Tabellen erfolgreich initialisiert');
  } catch (error) {
    console.error('Fehler beim Initialisieren der Datenbank:', error);
  }
}

// Datenbank beim Start initialisieren
initializeDatabase();

// Newsletter Route
app.post('/api/newsletter/subscribe', async (req, res) => {
  try {
    const { email, type } = req.body;

    // E-Mail validieren
    if (!email || !email.includes('@')) {
      return res.status(400).json({ 
        error: 'Bitte geben Sie eine gültige E-Mail-Adresse ein.' 
      });
    }

    // Typ validieren
    if (!['customer', 'vendor'].includes(type)) {
      return res.status(400).json({ 
        error: 'Bitte wählen Sie einen gültigen Anmeldetyp.' 
      });
    }

    // Prüfen, ob die E-Mail bereits existiert
    const [rows] = await pool.execute(
      'SELECT * FROM newsletter WHERE email = ?', 
      [email]
    );

    if (rows.length > 0) {
      return res.status(400).json({ 
        error: 'Diese E-Mail-Adresse ist bereits für den Newsletter registriert.' 
      });
    }

    // Neue Anmeldung erstellen
    await pool.execute(
      'INSERT INTO newsletter (email, type) VALUES (?, ?)',
      [email, type]
    );

    res.status(201).json({ 
      message: 'Newsletter-Anmeldung erfolgreich!' 
    });

  } catch (error) {
    console.error('Newsletter subscription error:', error);
    res.status(500).json({ 
      error: 'Bei der Newsletter-Anmeldung ist ein Fehler aufgetreten.' 
    });
  }
});

// Test Route
app.get('/api/newsletter/test', async (req, res) => {
  try {
    const [rows] = await pool.execute('SELECT 1 as test');
    res.json({ message: 'Backend und Datenbank sind erreichbar!', test: rows[0].test });
  } catch (error) {
    res.status(500).json({ error: 'Datenbankfehler: ' + error.message });
  }
});

// Listenanzeige für Admin
app.get('/api/newsletter/list', async (req, res) => {
  try {
    const [rows] = await pool.execute('SELECT * FROM newsletter');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: 'Fehler beim Abrufen der Newsletter-Liste' });
  }
});

// Server starten
const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
  console.log(`Server läuft auf Port ${PORT}`);
});