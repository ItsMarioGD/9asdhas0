USE medi_agenda_db;

-- Tabla de Usuarios para el Login
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Hash
    role ENUM('admin', 'doctor', 'recepcion') DEFAULT 'admin',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insertar usuario admin por defecto (Password: admin123)
-- El hash es generado para 'admin123'
INSERT IGNORE INTO users (username, password, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Asegurar que appointments tenga estado 'cancelled'
ALTER TABLE appointments 
MODIFY COLUMN status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled';
