<?php
include '../conexion.php';

// Función para obtener productos con filtros
function getProductos($filtro = 'default') {
    global $conn;
    $sql = "SELECT p.*, pr.nombre as proveedor_nombre FROM productos p LEFT JOIN proveedores pr ON p.id_proveedor = pr.id WHERE p.activo = 1";

    switch ($filtro) {
        case 'mas-stock':
            $sql .= " ORDER BY p.stock DESC";
            break;
        case 'menos-stock':
            $sql .= " ORDER BY p.stock ASC";
            break;
        case 'mas-vendido':
            $sql = "SELECT p.*, pr.nombre as proveedor_nombre, COALESCE(SUM(dv.cantidad), 0) as vendido FROM productos p LEFT JOIN proveedores pr ON p.id_proveedor = pr.id LEFT JOIN detalle_ventas dv ON p.id = dv.id_producto WHERE p.activo = 1 GROUP BY p.id ORDER BY vendido DESC";
            break;
        case 'menos-vendido':
            $sql = "SELECT p.*, pr.nombre as proveedor_nombre, COALESCE(SUM(dv.cantidad), 0) as vendido FROM productos p LEFT JOIN proveedores pr ON p.id_proveedor = pr.id LEFT JOIN detalle_ventas dv ON p.id = dv.id_producto WHERE p.activo = 1 GROUP BY p.id ORDER BY vendido ASC";
            break;
        case 'mayor-ganancia':
            $sql .= " ORDER BY (p.precio_venta - p.precio_compra) DESC";
            break;
        default:
            $sql .= " ORDER BY p.nombre ASC";
    }

    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para insertar producto
function insertProducto($nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor) {
    global $conn;
    $sql = "INSERT INTO productos (nombre, descripcion, stock, precio_compra, precio_venta, id_proveedor) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiddi", $nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor);
    return $stmt->execute();
}

// Función para ocultar producto
function ocultarProducto($id) {
    global $conn;
    $sql = "UPDATE productos SET activo = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Función para editar producto
function editarProducto($id, $nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor) {
    global $conn;
    $sql = "UPDATE productos SET nombre = ?, descripcion = ?, stock = ?, precio_compra = ?, precio_venta = ?, id_proveedor = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiddi", $nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor, $id);
    return $stmt->execute();
}

// Función para obtener un producto por ID
function getProductoById($id) {
    global $conn;
    $sql = "SELECT * FROM productos WHERE id = ? AND activo = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para obtener productos con bajo stock
function getProductosBajoStock($umbral = 10) {
    global $conn;
    $sql = "SELECT * FROM productos WHERE stock <= ? AND activo = 1 ORDER BY stock ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $umbral);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>