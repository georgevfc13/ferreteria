<?php
include '../conexion.php';

// ============================================
// MODEL: Ventas
// Maneja lógica de datos de ventas
// ============================================

if (!function_exists('getVentasDelDia')) {
    function getVentasDelDia() {
        global $conn;
        $sql = "SELECT 
                COALESCE(SUM(total), 0) as total_ventas, 
                COUNT(*) as num_transacciones 
                FROM ventas 
                WHERE DATE(fecha) = CURDATE()";
        $result = $conn->query($sql);
        return $result->fetch_assoc();
    }
}

if (!function_exists('getProductosMasVendidos')) {
    function getProductosMasVendidos($limit = 5) {
        global $conn;
        $sql = "SELECT p.nombre, COALESCE(SUM(dv.cantidad), 0) as total_vendido 
                FROM productos p 
                LEFT JOIN detalle_ventas dv ON p.id = dv.id_producto 
                WHERE p.activo = 1 
                GROUP BY p.id 
                ORDER BY total_vendido DESC 
                LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

if (!function_exists('getIngresosNetosSemanales')) {
    function getIngresosNetosSemanales() {
        global $conn;
        $sql = "SELECT SUM(monto) as ingresos FROM ingresos_egresos WHERE tipo = 'ingreso' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $result_ing = $conn->query($sql);
        $ingresos = $result_ing->fetch_assoc()['ingresos'] ?? 0;

        $sql = "SELECT SUM(monto) as egresos FROM ingresos_egresos WHERE tipo = 'egreso' AND fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $result_egr = $conn->query($sql);
        $egresos = $result_egr->fetch_assoc()['egresos'] ?? 0;

        return $ingresos - $egresos;
    }
}

if (!function_exists('getDatosGraficaSemanal')) {
    function getDatosGraficaSemanal() {
        global $conn;
        
        $dias = [];
        $ingresos = [];
        $egresos = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $timestamp = strtotime($fecha);
            $dia_numero = date('N', $timestamp);
            
            $nombres_dias = [
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
                7 => 'Domingo'
            ];
            
            $dias[] = $nombres_dias[$dia_numero];
            
            $sql_ing = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos_egresos 
                        WHERE tipo = 'ingreso' AND DATE(fecha) = ? AND activo = 1";
            $stmt_ing = $conn->prepare($sql_ing);
            $stmt_ing->bind_param("s", $fecha);
            $stmt_ing->execute();
            $result_ing = $stmt_ing->get_result();
            $ingresos[] = floatval($result_ing->fetch_assoc()['total']);
            
            $sql_egr = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos_egresos 
                        WHERE tipo = 'egreso' AND DATE(fecha) = ? AND activo = 1";
            $stmt_egr = $conn->prepare($sql_egr);
            $stmt_egr->bind_param("s", $fecha);
            $stmt_egr->execute();
            $result_egr = $stmt_egr->get_result();
            $egresos[] = floatval($result_egr->fetch_assoc()['total']);
        }

        return ['dias' => $dias, 'ingresos' => $ingresos, 'egresos' => $egresos];
    }
}
?>
