<?php
require_once 'db.php';

try {
    // 1. Crear la tabla si no existe (por seguridad)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'doctor', 'recepcion') DEFAULT 'admin',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // 2. Generar el hash correcto para 'admin123'
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // 3. Insertar o Actualizar el usuario admin
    $stmt = $pdo->prepare("
        INSERT INTO users (username, password, role) 
        VALUES ('admin', ?, 'admin') 
        ON DUPLICATE KEY UPDATE password = ?
    ");
    
    $stmt->execute([$hash, $hash]);

    echo "<h1>✅ ¡Login Reparado!</h1>";
    echo "<p>El usuario <strong>admin</strong> ha sido actualizado correctamente.</p>";
    echo "<ul>";
    echo "<li>Usuario: <strong>admin</strong></li>";
    echo "<li>Contraseña: <strong>admin123</strong></li>";
    echo "</ul>";
    echo "<br><a href='login.php' style='padding:10px 20px; background:blue; color:white; text-decoration:none; border-radius:5px;'>Ir al Login</a>";

} catch (PDOException $e) {
    echo "<h1>❌ Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
