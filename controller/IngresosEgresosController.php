<?php
include '../conexion.php';

// Función para obtener ingresos y egresos detallados semanales
function getIngresosEgresosSemanales() {
    global $conn;
    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    $data = [];

    for ($i = 6; $i >= 0; $i--) {
        $fecha = date('Y-m-d', strtotime("-$i days"));
        $dia = $dias[6 - $i];

        $sql_ing = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos_egresos WHERE tipo = 'ingreso' AND DATE(fecha) = '$fecha' AND activo = 1";
        $result_ing = $conn->query($sql_ing);
        $ingresos = floatval($result_ing->fetch_assoc()['total'] ?? 0);

        $sql_egr = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos_egresos WHERE tipo = 'egreso' AND DATE(fecha) = '$fecha' AND activo = 1";
        $result_egr = $conn->query($sql_egr);
        $egresos = floatval($result_egr->fetch_assoc()['total'] ?? 0);

        $data[] = [
            'dia' => $dia,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'neto' => $ingresos - $egresos
        ];
    }

    return $data;
}

// Función para obtener todos los ingresos/egresos activos
function getAllIngresosEgresos() {
    global $conn;
    $sql = "SELECT * FROM ingresos_egresos WHERE activo = 1 ORDER BY fecha DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para insertar nuevo ingreso/egreso con venta
function insertIngresoEgreso($tipo, $monto, $descripcion, $categoria, $id_producto = null, $cantidad = null) {
    global $conn;
    $monto = floatval($monto);
    $metodo_pago = 'efectivo'; // Por defecto
    
    // Si es una venta, primero insertar en tabla ventas
    $venta_id = null;
    if ($tipo === 'ingreso' && $id_producto && $cantidad) {
        $cliente = "Cliente";
        $total = $monto;
        $sql_venta_principal = "INSERT INTO ventas (total, cliente) VALUES (?, ?)";
        $stmt_venta_principal = $conn->prepare($sql_venta_principal);
        $stmt_venta_principal->bind_param("ds", $total, $cliente);
        $stmt_venta_principal->execute();
        $venta_id = $conn->insert_id;
    }
    
    // Insertar en ingresos_egresos
    $sql = "INSERT INTO ingresos_egresos (tipo, monto, descripcion, categoria, metodo_pago) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Error preparing statement: " . $conn->error);
        return false;
    }
    $stmt->bind_param("sdsss", $tipo, $monto, $descripcion, $categoria, $metodo_pago);
    $result = $stmt->execute();
    
    if (!$result) {
        error_log("Error executing statement: " . $stmt->error);
        return false;
    }
    
    // Si es una venta, registrar en detalle_ventas y actualizar stock
    if ($tipo === 'ingreso' && $venta_id && $id_producto && $cantidad) {
        $precio_unitario = $monto / $cantidad;
        $subtotal = $monto;
        
        // Registrar en detalle_ventas con el venta_id correcto
        $sql_venta_detalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt_venta_detalle = $conn->prepare($sql_venta_detalle);
        $stmt_venta_detalle->bind_param("iiidd", $venta_id, $id_producto, $cantidad, $precio_unitario, $subtotal);
        if (!$stmt_venta_detalle->execute()) {
            error_log("Error inserting detalle_ventas: " . $stmt_venta_detalle->error);
        }
        
        // Actualizar stock del producto
        $sql_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
        $stmt_stock = $conn->prepare($sql_stock);
        $stmt_stock->bind_param("ii", $cantidad, $id_producto);
        if (!$stmt_stock->execute()) {
            error_log("Error updating stock: " . $stmt_stock->error);
        }
    }
    
    return $result;
}

// Función para "eliminar" (ocultar) ingreso/egreso
function ocultarIngresoEgreso($id) {
    global $conn;
    $sql = "UPDATE ingresos_egresos SET activo = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>