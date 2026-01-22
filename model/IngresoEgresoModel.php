<?php
include '../conexion.php';

// ============================================
// MODEL: Ingresos y Egresos
// Maneja toda la lógica de acceso a datos
// ============================================

if (!function_exists('getIngresosEgresosSemanales')) {
    function getIngresosEgresosSemanales() {
        global $conn;
        $data = [];
        
        $nombres_dias = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo'
        ];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $timestamp = strtotime($fecha);
            $dia_numero = date('N', $timestamp);
            $dia_nombre = $nombres_dias[$dia_numero];

            $sql_ing = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos_egresos 
                        WHERE tipo = 'ingreso' AND DATE(fecha) = ? AND activo = 1";
            $stmt_ing = $conn->prepare($sql_ing);
            $stmt_ing->bind_param("s", $fecha);
            $stmt_ing->execute();
            $result_ing = $stmt_ing->get_result();
            $ingresos = floatval($result_ing->fetch_assoc()['total'] ?? 0);

            $sql_egr = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos_egresos 
                        WHERE tipo = 'egreso' AND DATE(fecha) = ? AND activo = 1";
            $stmt_egr = $conn->prepare($sql_egr);
            $stmt_egr->bind_param("s", $fecha);
            $stmt_egr->execute();
            $result_egr = $stmt_egr->get_result();
            $egresos = floatval($result_egr->fetch_assoc()['total'] ?? 0);

            $data[] = [
                'dia' => $dia_nombre,
                'fecha' => $fecha,
                'ingresos' => $ingresos,
                'egresos' => $egresos,
                'neto' => $ingresos - $egresos
            ];
        }

        return $data;
    }
}

if (!function_exists('getAllIngresosEgresos')) {
    function getAllIngresosEgresos() {
        global $conn;
        $sql = "SELECT * FROM ingresos_egresos WHERE activo = 1 ORDER BY fecha DESC";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

if (!function_exists('insertIngresoEgreso')) {
    function insertIngresoEgreso($tipo, $monto, $descripcion, $categoria, $id_producto = null, $cantidad = null) {
        global $conn;
        $monto = floatval($monto);
        $metodo_pago = 'efectivo';
        
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
        
        if ($tipo === 'ingreso' && $venta_id && $id_producto && $cantidad) {
            $precio_unitario = $monto / $cantidad;
            $subtotal = $monto;
            
            $sql_venta_detalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt_venta_detalle = $conn->prepare($sql_venta_detalle);
            $stmt_venta_detalle->bind_param("iiidd", $venta_id, $id_producto, $cantidad, $precio_unitario, $subtotal);
            if (!$stmt_venta_detalle->execute()) {
                error_log("Error inserting detalle_ventas: " . $stmt_venta_detalle->error);
            }
            
            $sql_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
            $stmt_stock = $conn->prepare($sql_stock);
            $stmt_stock->bind_param("ii", $cantidad, $id_producto);
            if (!$stmt_stock->execute()) {
                error_log("Error updating stock: " . $stmt_stock->error);
            }
        }
        
        return $result;
    }
}

if (!function_exists('ocultarIngresoEgreso')) {
    function ocultarIngresoEgreso($id) {
        global $conn;
        $sql = "UPDATE ingresos_egresos SET activo = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
