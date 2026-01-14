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

        $sql_ing = "SELECT SUM(monto) as total FROM ingresos_egresos WHERE tipo = 'ingreso' AND DATE(fecha) = '$fecha' AND activo = 1";
        $result_ing = $conn->query($sql_ing);
        $ingresos = $result_ing->fetch_assoc()['total'] ?? 0;

        $sql_egr = "SELECT SUM(monto) as total FROM ingresos_egresos WHERE tipo = 'egreso' AND DATE(fecha) = '$fecha' AND activo = 1";
        $result_egr = $conn->query($sql_egr);
        $egresos = $result_egr->fetch_assoc()['total'] ?? 0;

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

// Función para insertar nuevo ingreso/egreso
function insertIngresoEgreso($tipo, $monto, $descripcion, $categoria) {
    global $conn;
    $monto = floatval($monto);
    $sql = "INSERT INTO ingresos_egresos (tipo, monto, descripcion, categoria) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Error preparing statement: " . $conn->error);
        return false;
    }
    $stmt->bind_param("sdss", $tipo, $monto, $descripcion, $categoria);
    $result = $stmt->execute();
    if (!$result) {
        error_log("Error executing statement: " . $stmt->error);
        return false;
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