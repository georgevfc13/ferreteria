<?php
$pageTitle = 'Cierre de Caja';
$pageCSS = ['../css/cierre_caja.css'];
include '../conexion.php';

// Obtener fecha actual
$fecha = date('Y-m-d');

// Calcular saldo en sistema (ventas efectivo - egresos efectivo del día)
$sql_ingresos = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos_egresos 
                 WHERE tipo = 'ingreso' AND metodo_pago = 'efectivo' AND DATE(fecha) = ? AND activo = 1";
$stmt_ing = $conn->prepare($sql_ingresos);
$stmt_ing->bind_param("s", $fecha);
$stmt_ing->execute();
$resultado_ing = $stmt_ing->get_result()->fetch_assoc();
$ingresos_efectivo = $resultado_ing['total'];

$sql_egresos = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos_egresos 
                WHERE tipo = 'egreso' AND metodo_pago = 'efectivo' AND DATE(fecha) = ? AND activo = 1";
$stmt_egr = $conn->prepare($sql_egresos);
$stmt_egr->bind_param("s", $fecha);
$stmt_egr->execute();
$resultado_egr = $stmt_egr->get_result()->fetch_assoc();
$egresos_efectivo = $resultado_egr['total'];

$saldo_sistema = $ingresos_efectivo - $egresos_efectivo;

// Procesar envío del formulario
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cierre_caja'])) {
    $saldo_real = floatval($_POST['saldo_real']);
    $observaciones = $_POST['observaciones'] ?? '';
    
    $diferencia = abs($saldo_sistema - $saldo_real);
    $tipo_diferencia = $saldo_real > $saldo_sistema ? 'sobrante' : 'faltante';
    
    // Guardar en tabla arqueos_caja
    $sql_arqueo = "INSERT INTO arqueos_caja (fecha, saldo_sistema, saldo_real, diferencia, tipo_diferencia, observaciones) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_arqueo = $conn->prepare($sql_arqueo);
    $stmt_arqueo->bind_param("sddsss", $fecha, $saldo_sistema, $saldo_real, $diferencia, $tipo_diferencia, $observaciones);
    
    if ($stmt_arqueo->execute()) {
        $tipo_mensaje = 'success';
        $mensaje = 'Cierre de caja registrado correctamente';
    } else {
        $tipo_mensaje = 'error';
        $mensaje = 'Error al guardar el cierre de caja';
    }
}

include '../template/header.php';
?>

<link rel="stylesheet" href="../css/cierre_caja.css">

<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="print-header print-hide">
        <h1 class="text-4xl font-bold mb-4 text-center text-dark">Cierre de Caja</h1>
        <p class="text-sm text-gray-500">Fecha: <?php echo date('d/m/Y'); ?></p>
        <p class="text-xs text-gray-400 print-hide">Impresión: <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <?php if ($mensaje): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $tipo_mensaje === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> print-hide">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 print-hide">
        <!-- Saldo en Sistema -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-600">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Saldo en Sistema</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Ingresos en Efectivo:</span>
                    <span class="font-bold text-green-600">+$<?php echo number_format($ingresos_efectivo, 2); ?></span>
                </div>
                <div class="flex justify-between border-b pb-3">
                    <span class="text-gray-600">Egresos en Efectivo:</span>
                    <span class="font-bold text-red-600">-$<?php echo number_format($egresos_efectivo, 2); ?></span>
                </div>
                <div class="flex justify-between pt-3">
                    <span class="text-lg font-bold text-gray-800">Total Sistema:</span>
                    <span class="text-2xl font-bold text-blue-600">$<?php echo number_format($saldo_sistema, 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Formulario de Saldo Real -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-orange-600 print-hide">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Saldo Físico Contado</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="saldo_real" class="block text-sm font-medium text-gray-700 mb-2">
                        Efectivo Contado
                    </label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="saldo_real" 
                        id="saldo_real" 
                        required 
                        placeholder="0.00"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-dark"
                    >
                </div>
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones
                    </label>
                    <textarea 
                        name="observaciones" 
                        id="observaciones" 
                        rows="3"
                        placeholder="Notas adicionales del cierre"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-dark"
                    ></textarea>
                </div>
                <button 
                    type="submit" 
                    name="cierre_caja" 
                    class="w-full bg-orange-600 text-white font-bold py-3 rounded-lg hover:bg-orange-700 transition duration-300"
                >
                    <i class="fas fa-lock mr-2"></i>Registrar Cierre
                </button>
            </form>
        </div>
    </div>

    <!-- Comparativa de Saldos (si se envió el formulario) -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cierre_caja'])): ?>
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 print-hide">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Comparativa de Saldos</h2>
            
            <?php 
            $saldo_real = floatval($_POST['saldo_real']);
            $diferencia = abs($saldo_sistema - $saldo_real);
            $tipo_diferencia = $saldo_real > $saldo_sistema ? 'sobrante' : 'faltante';
            ?>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <p class="text-gray-600 text-sm mb-2">Saldo en Sistema</p>
                    <p class="text-3xl font-bold text-blue-600">$<?php echo number_format($saldo_sistema, 2); ?></p>
                </div>

                <div class="bg-orange-50 p-4 rounded-lg text-center">
                    <p class="text-gray-600 text-sm mb-2">Saldo Real</p>
                    <p class="text-3xl font-bold text-orange-600">$<?php echo number_format($saldo_real, 2); ?></p>
                </div>

                <div class="bg-<?php echo $tipo_diferencia === 'sobrante' ? 'green' : 'red'; ?>-50 p-4 rounded-lg text-center">
                    <p class="text-gray-600 text-sm mb-2"><?php echo ucfirst($tipo_diferencia); ?></p>
                    <p class="text-3xl font-bold text-<?php echo $tipo_diferencia === 'sobrante' ? 'green' : 'red'; ?>-600">
                        $<?php echo number_format($diferencia, 2); ?>
                    </p>
                </div>
            </div>

            <div class="mt-4 text-center">
                <?php if ($diferencia == 0): ?>
                    <div class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg">
                        <p class="text-lg font-bold">✓ COMPLETO - Caja Cuadrada</p>
                    </div>
                <?php else: ?>
                    <p class="text-<?php echo $tipo_diferencia === 'sobrante' ? 'green' : 'red'; ?>-600 text-lg font-bold">
                        <?php echo ucfirst($tipo_diferencia); ?> de $<?php echo number_format($diferencia, 2); ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($_POST['observaciones'])): ?>
            <div class="mt-4 p-3 bg-gray-50 rounded border-l-4 border-gray-400">
                <p class="text-sm text-gray-600"><strong>Observaciones:</strong></p>
                <p class="text-gray-700"><?php echo htmlspecialchars($_POST['observaciones']); ?></p>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Historico de Cierres -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Histórico de Cierres</h2>
        
        <?php
        $sql_historico = "SELECT * FROM arqueos_caja ORDER BY fecha DESC LIMIT 10";
        $resultado_historico = $conn->query($sql_historico);
        
        if ($resultado_historico && $resultado_historico->num_rows > 0):
        ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-dark">Fecha</th>
                            <th class="px-4 py-2 text-right text-dark">Saldo Sistema</th>
                            <th class="px-4 py-2 text-right text-dark">Saldo Real</th>
                            <th class="px-4 py-2 text-right text-dark">Diferencia</th>
                            <th class="px-4 py-2 text-left text-dark">Tipo</th>
                            <th class="px-4 py-2 text-center text-dark print-hide">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($arqueo = $resultado_historico->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-dark"><?php echo date('d/m/Y', strtotime($arqueo['fecha'])); ?></td>
                                <td class="px-4 py-3 text-right text-dark">$<?php echo number_format($arqueo['saldo_sistema'], 2); ?></td>
                                <td class="px-4 py-3 text-right text-dark">$<?php echo number_format($arqueo['saldo_real'], 2); ?></td>
                                <td class="px-4 py-3 text-right font-bold text-dark">$<?php echo number_format($arqueo['diferencia'], 2); ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($arqueo['diferencia'] == 0): ?>
                                        <span class="px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-800">
                                            Completo
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full text-sm font-bold <?php echo $arqueo['tipo_diferencia'] === 'sobrante' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo ucfirst($arqueo['tipo_diferencia']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center print-hide">
                                    <?php if (!empty($arqueo['observaciones'])): ?>
                                        <button onclick="mostrarObservaciones('<?php echo addslashes($arqueo['observaciones']); ?>', '<?php echo date('d/m/Y', strtotime($arqueo['fecha'])); ?>')" 
                                                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition duration-300 text-sm">
                                            <i class="fas fa-eye mr-1"></i>Ver
                                        </button>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">Sin observaciones</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-600 text-center py-4">No hay cierres registrados aún</p>
        <?php endif; ?>
    </div>

    <div class="mt-8 flex gap-4 print-hide">
        <a href="home.php" class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition duration-300">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Inicio
        </a>
        
        <button onclick="window.print()" class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300">
            <i class="fas fa-print mr-2"></i>Imprimir Cierre de Caja
        </button>
    </div>
</section>

<!-- Modal para mostrar observaciones -->
<div id="modalObservaciones" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 print-hide">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitulo">Observaciones del Cierre</h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-600 mb-2">Fecha: <strong id="modalFecha"></strong></p>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-700" id="modalContenido"></p>
                </div>
            </div>
            <div class="flex items-center justify-center px-4 py-3 mt-4">
                <button onclick="cerrarModal()" class="px-6 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$stmt_ing->close();
$stmt_egr->close();
include '../template/footer.php';
?>

<script src="../js/cierre_caja.js"></script>