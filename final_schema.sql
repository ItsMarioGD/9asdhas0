-- Base de datos para MediAgenda
CREATE DATABASE IF NOT EXISTS medi_agenda_db;
USE medi_agenda_db;
-- 1. Tabla de Usuarios (Administradores, Doctores, Recepción)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    -- Hash
    role ENUM('admin', 'doctor', 'recepcion') DEFAULT 'admin',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
-- 2. Tabla de Doctores
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialty VARCHAR(100) NOT NULL
);
-- 3. Tabla de Pacientes
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL
);
-- 4. Tabla de Citas (Appointments)
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT,
    patient_id INT,
    date_time DATETIME NOT NULL,
    reason TEXT,
    appointment_type ENUM('consulta', 'laboratorio', 'chequeo') DEFAULT 'consulta',
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    UNIQUE KEY unique_appointment (doctor_id, date_time)
);
-- 5. Tabla de Consultas (Detalles médicos de la cita)
CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT,
    symptoms TEXT,
    diagnosis TEXT,
    medication TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);
-- 6. Tabla de Horarios Bloqueados
CREATE TABLE IF NOT EXISTS blocked_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    reason VARCHAR(255) DEFAULT 'No disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);
-- ==========================================
-- DATOS INICIALES (SEED DATA)
-- ==========================================
-- Insertar usuario admin por defecto (Password: admin123)
INSERT IGNORE INTO users (username, password, role)
VALUES (
        'admin',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'admin'
    );
-- Insertar doctores de ejemplo (si no existen)
INSERT INTO doctors (name, specialty)
SELECT *
FROM (
        SELECT 'Dr. House',
            'Diagnosta'
    ) AS tmp
WHERE NOT EXISTS (
        SELECT name
        FROM doctors
        WHERE name = 'Dr. House'
    )
LIMIT 1;
INSERT INTO doctors (name, specialty)
SELECT *
FROM (
        SELECT 'Dra. Grey',
            'Cirujana'
    ) AS tmp
WHERE NOT EXISTS (
        SELECT name
        FROM doctors
        WHERE name = 'Dra. Grey'
    )
LIMIT 1;