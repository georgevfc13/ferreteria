<?php
// ============================================
// CONTROLLER: Inventario
// Orquesta: entrada del usuario -> model -> vista
// ============================================

// Cargar los modelos
include '../model/ProductoModel.php';
include '../model/ProveedorModel.php';

// Variables iniciales
$pageTitle = 'Inventario de Productos';
$pageCSS = ['../css/ingresos_egresos.css'];
$error = null;
$success = null;

// 1. PROCESAR ENTRADA DEL USUARIO (POST/GET)
// ============================================

// Obtener filtro si existe
$filtro = $_GET['filtro'] ?? 'default';

// Agregar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_producto'])) {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $stock = (int)($_POST['stock'] ?? 0);
    $precio_compra = (float)($_POST['precio_compra'] ?? 0);
    $precio_venta = (float)($_POST['precio_venta'] ?? 0);
    $id_proveedor = (int)($_POST['id_proveedor'] ?? 0);
    
    if (insertProducto($nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor)) {
        $success = "Producto agregado exitosamente";
        header("Location: inventario.php");
        exit();
    } else {
        $error = "Error al agregar el producto";
    }
}

// Editar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_producto'])) {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $stock = (int)($_POST['stock'] ?? 0);
    $precio_compra = (float)($_POST['precio_compra'] ?? 0);
    $precio_venta = (float)($_POST['precio_venta'] ?? 0);
    $id_proveedor = (int)($_POST['id_proveedor'] ?? 0);
    
    if (editarProducto($id, $nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor)) {
        $success = "Producto actualizado exitosamente";
        header("Location: inventario.php");
        exit();
    } else {
        $error = "Error al actualizar el producto";
    }
}

// Eliminar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_producto'])) {
    $id = (int)($_POST['id'] ?? 0);
    if (ocultarProducto($id)) {
        $success = "Producto eliminado exitosamente";
        header("Location: inventario.php");
        exit();
    } else {
        $error = "Error al eliminar el producto";
    }
}

// 2. OBTENER DATOS DEL MODELO
// =============================
$productos = getProductos($filtro);
$proveedores = getProveedores();
$productos_bajo_stock = getProductosBajoStock();

// 3. LA VISTA SE CARGA A CONTINUACIÓN (en el archivo view)
?>