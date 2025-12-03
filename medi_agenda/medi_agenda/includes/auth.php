<?php
session_start();

// Función para verificar si el usuario está logueado
function requireLogin() {
    // Login desactivado a petición del usuario
    return true;
}

// Función para verificar si es admin
function requireAdmin() {
    return true;
}
?>
