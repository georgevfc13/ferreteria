<?php
include '../conexion.php';

// ============================================
// MODEL: Proveedores
// Maneja toda la lógica de acceso a datos
// ============================================

if (!function_exists('getProveedores')) {
    function getProveedores() {
        global $conn;
        $sql = "SELECT * FROM proveedores WHERE activo = 1 ORDER BY nombre ASC";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

if (!function_exists('insertProveedor')) {
    function insertProveedor($nombre, $contacto, $telefono, $email, $direccion) {
        global $conn;
        $sql = "INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $contacto, $telefono, $email, $direccion);
        return $stmt->execute();
    }
}

if (!function_exists('ocultarProveedor')) {
    function ocultarProveedor($id) {
        global $conn;
        $sql = "UPDATE proveedores SET activo = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
