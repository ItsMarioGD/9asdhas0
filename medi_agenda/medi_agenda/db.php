<?php

/**
 * Medi-Agenda AI - Database Connection
 * Supports both SQLite (local file) and MySQL (server)
 * Auto-detects which one to use
 */

// Prevent any output before JSON headers
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $envVars = parse_ini_file(__DIR__ . '/.env');
    foreach ($envVars as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Database configuration
$useMysql = false;
$pdo = null;

// Try MySQL first if credentials exist
if (isset($_ENV['DB_HOST']) && isset($_ENV['DB_NAME']) && isset($_ENV['DB_USER'])) {
    try {
        $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
        $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'] ?? '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $useMysql = true;

        // Success message (silent in production)
        if (isset($_GET['debug'])) {
            echo "<!-- Database: MySQL (Server) -->\n";
        }
    } catch (PDOException $e) {
        // MySQL failed, will try SQLite
        $useMysql = false;
    }
}

// Fallback to SQLite if MySQL not available
if (!$useMysql || $pdo === null) {
    try {
        $dbFile = __DIR__ . '/data/medi_agenda.db';

        // Create data directory if doesn't exist
        if (!file_exists(dirname($dbFile))) {
            mkdir(dirname($dbFile), 0755, true);
        }

        $isNewDatabase = !file_exists($dbFile);

        $pdo = new PDO("sqlite:$dbFile");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Enable foreign keys in SQLite
        $pdo->exec("PRAGMA foreign_keys = ON");

        // Initialize database if new
        if ($isNewDatabase) {
            initializeSQLiteDatabase($pdo);
        }

        if (isset($_GET['debug'])) {
            echo "<!-- Database: SQLite (Local File) -->\n";
        }
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

/**
 * Initialize SQLite database with schema
 */
function initializeSQLiteDatabase($pdo)
{
    $schema = "
    CREATE TABLE IF NOT EXISTS doctors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        specialty TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS patients (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        phone TEXT,
        email TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        doctor_id INTEGER NOT NULL,
        patient_id INTEGER NOT NULL,
        date_time DATETIME NOT NULL,
        reason TEXT,
        status TEXT DEFAULT 'scheduled' CHECK(status IN ('scheduled', 'completed', 'cancelled')),
        appointment_type TEXT DEFAULT 'consulta' CHECK(appointment_type IN ('consulta', 'laboratorio', 'chequeo')),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
        FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS consultations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        appointment_id INTEGER NOT NULL,
        diagnosis TEXT NOT NULL,
        medication TEXT NOT NULL,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS blocked_slots (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        doctor_id INTEGER NOT NULL,
        start_time DATETIME NOT NULL,
        end_time DATETIME NOT NULL,
        reason TEXT DEFAULT 'No disponible',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
    );

    -- Insert sample doctor if none exist
    INSERT INTO doctors (name, specialty) 
    SELECT 'Dr. Demo', 'Medicina General'
    WHERE NOT EXISTS (SELECT 1 FROM doctors);
    ";

    try {
        $pdo->exec($schema);
    } catch (PDOException $e) {
        die("Failed to initialize database: " . $e->getMessage());
    }
}

/**
 * Get API Key from environment
 */
function getApiKey()
{
    return $_ENV['GROQ_API_KEY'] ?? '';
}

/**
 * Check if using MySQL or SQLite
 */
function getDatabaseType()
{
    global $useMysql;
    return $useMysql ? 'mysql' : 'sqlite';
}

/**
 * Get database info for admin purposes
 */
function getDatabaseInfo()
{
    global $pdo, $useMysql;

    if ($useMysql) {
        return [
            'type' => 'MySQL (Server)',
            'host' => $_ENV['DB_HOST'] ?? 'unknown',
            'database' => $_ENV['DB_NAME'] ?? 'unknown',
            'file' => null
        ];
    } else {
        return [
            'type' => 'SQLite (Local File)',
            'host' => 'localhost',
            'database' => 'medi_agenda.db',
            'file' => realpath(__DIR__ . '/data/medi_agenda.db')
        ];
    }
}
