<?php
include '../conexion.php';

// Función para obtener datos de ventas del día
function getVentasDelDia() {
    global $conn;
    $sql = "SELECT SUM(total) as total_ventas, COUNT(*) as num_transacciones FROM ventas WHERE DATE(fecha) = CURDATE()";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Función para obtener productos más vendidos
function getProductosMasVendidos($limit = 5) {
    global $conn;
    $sql = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido FROM productos p JOIN detalle_ventas dv ON p.id = dv.id_producto GROUP BY p.id ORDER BY total_vendido DESC LIMIT $limit";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener productos con bajo stock
function getProductosBajoStock($umbral = 10) {
    global $conn;
    $sql = "SELECT nombre, stock FROM productos WHERE stock <= $umbral ORDER BY stock ASC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener ingresos netos semanales
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

// Función para obtener datos de gráfica semanal
function getDatosGraficaSemanal() {
    global $conn;
    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    $ingresos = [];
    $egresos = [];

    for ($i = 6; $i >= 0; $i--) {
        $fecha = date('Y-m-d', strtotime("-$i days"));
        $sql_ing = "SELECT SUM(monto) as total FROM ingresos_egresos WHERE tipo = 'ingreso' AND DATE(fecha) = '$fecha' AND activo = 1";
        $result_ing = $conn->query($sql_ing);
        $ingresos[] = $result_ing->fetch_assoc()['total'] ?? 0;

        $sql_egr = "SELECT SUM(monto) as total FROM ingresos_egresos WHERE tipo = 'egreso' AND DATE(fecha) = '$fecha' AND activo = 1";
        $result_egr = $conn->query($sql_egr);
        $egresos[] = $result_egr->fetch_assoc()['total'] ?? 0;
    }

    return ['dias' => $dias, 'ingresos' => $ingresos, 'egresos' => $egresos];
}
?>