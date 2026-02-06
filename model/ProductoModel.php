<?php
include '../conexion.php';

if (!function_exists('getProductos')) {
    function getProductos($filtro = 'default') {
        global $conn;
        
        $sql = "SELECT p.*, 
                pr.nombre as proveedor_nombre, 
                COALESCE(SUM(dv.cantidad), 0) as vendido 
                FROM productos p 
                LEFT JOIN proveedores pr ON p.id_proveedor = pr.id 
                LEFT JOIN detalle_ventas dv ON p.id = dv.id_producto 
                WHERE p.activo = 1 
                GROUP BY p.id";

        switch ($filtro) {
            case 'mas-stock':
                $sql .= " ORDER BY p.stock DESC";
                break;
            case 'menos-stock':
                $sql .= " ORDER BY p.stock ASC";
                break;
            case 'mas-vendido':
                $sql .= " ORDER BY vendido DESC";
                break;
            case 'menos-vendido':
                $sql .= " ORDER BY vendido ASC";
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
}

if (!function_exists('insertProducto')) {
    function insertProducto($nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor) {
        global $conn;
        $sql = "INSERT INTO productos (nombre, descripcion, stock, precio_compra, precio_venta, id_proveedor) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiddi", $nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor);
        return $stmt->execute();
    }
}

if (!function_exists('ocultarProducto')) {
    function ocultarProducto($id) {
        global $conn;
        $sql = "UPDATE productos SET activo = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

if (!function_exists('editarProducto')) {
    function editarProducto($id, $nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor) {
        global $conn;
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, stock = ?, precio_compra = ?, precio_venta = ?, id_proveedor = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiidii", $nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor, $id);
        return $stmt->execute();
    }
}

if (!function_exists('getProductoById')) {
    function getProductoById($id) {
        global $conn;
        $sql = "SELECT * FROM productos WHERE id = ? AND activo = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

if (!function_exists('getProductosBajoStock')) {
    function getProductosBajoStock($umbral = 10) {
        global $conn;
        $sql = "SELECT * FROM productos WHERE stock <= ? AND activo = 1 ORDER BY stock ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $umbral);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
