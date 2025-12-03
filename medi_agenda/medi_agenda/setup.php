<?php

/**
 * Setup Script for Medi-Agenda AI
 * Initializes the database using credentials from .env
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $envVars = parse_ini_file(__DIR__ . '/.env');
    foreach ($envVars as $key => $value) {
        $_ENV[$key] = $value;
    }
} else {
    die("Error: No se encontró el archivo .env");
}

// Check required variables
if (!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_NAME']) || !isset($_ENV['DB_USER'])) {
    die("Error: Faltan variables de configuración de base de datos en .env");
}

try {
    echo "Intentando conectar a MySQL...<br>";

    $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'] ?? '');

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a la base de datos: " . htmlspecialchars($_ENV['DB_NAME']) . "<br>";

    // Read schema file
    $schemaFile = __DIR__ . '/final_schema.sql';
    if (!file_exists($schemaFile)) {
        die("Error: No se encontró final_schema.sql");
    }

    $sql = file_get_contents($schemaFile);

    // Split into queries
    // Remove comments and empty lines first to make parsing easier
    $lines = explode("\n", $sql);
    $cleanSql = "";
    foreach ($lines as $line) {
        $trimLine = trim($line);
        // Skip comments and empty lines
        if (empty($trimLine) || strpos($trimLine, '--') === 0) continue;

        // Skip CREATE DATABASE and USE commands as we are already connected
        if (stripos($trimLine, 'CREATE DATABASE') === 0) continue;
        if (stripos($trimLine, 'USE ') === 0) continue;

        $cleanSql .= $line . "\n";
    }

    $queries = explode(';', $cleanSql);

    echo "Ejecutando esquema...<br>";

    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            try {
                $pdo->exec($query);
                // echo "Ejecutado: " . substr($query, 0, 50) . "...<br>";
            } catch (PDOException $e) {
                echo "Advertencia en query: " . htmlspecialchars(substr($query, 0, 50)) . "... - " . $e->getMessage() . "<br>";
            }
        }
    }

    echo "<br><strong>¡Instalación completada exitosamente!</strong><br>";
    echo "Tablas creadas y datos iniciales insertados.<br>";
    echo "<br><a href='index.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>Ir al Inicio</a>";
} catch (PDOException $e) {
    die("Error Crítico: " . $e->getMessage());
}
