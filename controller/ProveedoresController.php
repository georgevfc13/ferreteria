<?php
include '../conexion.php';

// Función para obtener todos los proveedores
function getProveedores() {
    global $conn;
    $sql = "SELECT * FROM proveedores WHERE activo = 1 ORDER BY nombre ASC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para insertar proveedor
function insertProveedor($nombre, $contacto, $telefono, $email, $direccion) {
    global $conn;
    $sql = "INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $contacto, $telefono, $email, $direccion);
    return $stmt->execute();
}

// Función para ocultar proveedor
function ocultarProveedor($id) {
    global $conn;
    $sql = "UPDATE proveedores SET activo = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>