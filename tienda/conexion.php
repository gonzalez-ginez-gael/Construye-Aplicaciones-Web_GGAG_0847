<?php
// Conexión a la base de datos tienda_db
// Si tu MySQL tiene contraseña, escríbela entre las comillas vacías
$conexion = new mysqli("localhost", "root", "", "tienda_db");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4"); // para que se vean bien los acentos
session_start();                   // para recordar quién inició sesión
?>
