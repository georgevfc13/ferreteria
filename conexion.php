<?php
$servername = "localhost";  // Este es el servidor local
$username = "root";         // Por Xampp siempre sera root
$password = "";             // La contraseña en servidores locales es vacia
$dbname = "ferreteria";     // El nombre de mi base de datos

// Pa crear la conexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Pa ver si la conexion esta bien
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Si llega para aca entonces esta bien
// (Sin echo para evitar output en includes)
?>