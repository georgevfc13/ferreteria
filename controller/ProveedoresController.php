<?php
// ============================================
// CONTROLLER: Proveedores
// Orquesta: entrada del usuario -> model -> vista
// ============================================

// Cargar el modelo
include '../model/ProveedorModel.php';

// Variables iniciales
$pageTitle = 'Nuestros Proveedores';
$pageCSS = ['../css/proveedores.css'];
$error = null;
$success = null;

// 1. PROCESAR ENTRADA DEL USUARIO (POST)
// =========================================

// Agregar nuevo proveedor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'] ?? '';
    $contacto = $_POST['contacto'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    
    if (insertProveedor($nombre, $contacto, $telefono, $email, $direccion)) {
        $success = "Proveedor agregado exitosamente";
        header("Location: proveedores.php");
        exit();
    } else {
        $error = "Error al agregar el proveedor";
    }
}

// Eliminar proveedor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id = $_POST['id'] ?? null;
    $codigo = $_POST['codigo'] ?? '';
    $codigo_correcto = str_pad($id, 6, '0', STR_PAD_LEFT);
    
    if ($codigo === $codigo_correcto && ocultarProveedor($id)) {
        $success = "Proveedor eliminado exitosamente";
        header("Location: proveedores.php");
        exit();
    } else {
        $error = "Código de confirmación incorrecto";
    }
}

// 2. OBTENER DATOS DEL MODELO
// =============================
$proveedores = getProveedores();

// 3. LA VISTA SE CARGA A CONTINUACIÓN (en el archivo view)
?>