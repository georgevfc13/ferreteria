<?php
$pageTitle = 'Kardex de Inventario';
include '../conexion.php';

// Obtener el ID del producto desde la solicitud
$producto_id = isset($_GET['producto_id']) ? intval($_GET['producto_id']) : 0;

if ($producto_id <= 0) {
    die("ID de producto inválido");
}

// Obtener información del producto
$sql_producto = "SELECT nombre, precio_compra FROM productos WHERE id = ? AND activo = 1";
$stmt_producto = $conn->prepare($sql_producto);
$stmt_producto->bind_param("i", $producto_id);
$stmt_producto->execute();
$result_producto = $stmt_producto->get_result();
$producto = $result_producto->fetch_assoc();

if (!$producto) {
    die("Producto no encontrado");
}

// Obtener movimientos de detalle_ventas (salidas/ventas)
$sql = "SELECT 
    dv.id,
    v.fecha,
    'Salida' as tipo_movimiento,
    dv.cantidad,
    dv.precio_unitario,
    dv.cantidad * dv.precio_unitario as total_movimiento
FROM detalle_ventas dv
JOIN ventas v ON dv.id_venta = v.id
WHERE dv.id_producto = ?
ORDER BY v.fecha ASC, dv.id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$result = $stmt->get_result();
$movimientos = $result->fetch_all(MYSQLI_ASSOC);

// Calcular saldo acumulado usando promedio ponderado
$saldo_cantidad = 0;
$saldo_valor = 0;
$precio_promedio = $producto['precio_compra'];

foreach ($movimientos as &$mov) {
    if ($mov['tipo_movimiento'] == 'Entrada') {
        // Entrada: recalcular promedio ponderado
        $cantidad_antes = $saldo_cantidad;
        $valor_antes = $saldo_valor;
        
        $saldo_cantidad += $mov['cantidad'];
        $saldo_valor += $mov['total_movimiento'];
        
        if ($saldo_cantidad > 0) {
            $precio_promedio = $saldo_valor / $saldo_cantidad;
        }
    } else {
        // Salida: usar precio promedio actual
        $saldo_cantidad -= $mov['cantidad'];
        $saldo_valor -= ($mov['cantidad'] * $precio_promedio);
        
        if ($saldo_valor < 0) $saldo_valor = 0;
        if ($saldo_cantidad < 0) $saldo_cantidad = 0;
    }
    
    $mov['saldo_cantidad'] = $saldo_cantidad;
    $mov['saldo_valor'] = $saldo_valor;
    $mov['precio_promedio'] = $precio_promedio;
}

include '../template/header.php';
?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2">Kardex de Inventario</h1>
        <p class="text-xl text-gray-600">Producto: <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong></p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left">Fecha</th>
                    <th class="px-6 py-3 text-left">Tipo</th>
                    <th class="px-6 py-3 text-right">Cantidad</th>
                    <th class="px-6 py-3 text-right">Precio Unitario</th>
                    <th class="px-6 py-3 text-right">Saldo Cantidad</th>
                    <th class="px-6 py-3 text-right">Precio Promedio</th>
                    <th class="px-6 py-3 text-right">Saldo Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($movimientos)) {
                    foreach ($movimientos as $mov) {
                        $clase_tipo = $mov['tipo_movimiento'] === 'Entrada' ? 'text-green-600' : 'text-red-600';
                        echo '<tr class="border-b hover:bg-gray-50">'; 
                        echo '<td class="px-6 py-4">' . date('d/m/Y H:i', strtotime($mov['fecha'])) . '</td>'; 
                        echo '<td class="px-6 py-4 ' . $clase_tipo . '"><strong>' . htmlspecialchars($mov['tipo_movimiento']) . '</strong></td>'; 
                        echo '<td class="px-6 py-4 text-right">' . number_format($mov['cantidad'], 0) . '</td>'; 
                        echo '<td class="px-6 py-4 text-right">$' . number_format($mov['precio_unitario'], 2) . '</td>'; 
                        echo '<td class="px-6 py-4 text-right font-bold">' . number_format($mov['saldo_cantidad'], 0) . '</td>'; 
                        echo '<td class="px-6 py-4 text-right">$' . number_format($mov['precio_promedio'], 2) . '</td>'; 
                        echo '<td class="px-6 py-4 text-right font-bold">$' . number_format($mov['saldo_valor'], 2) . '</td>'; 
                        echo '</tr>'; 
                    }
                } else {
                    echo '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay movimientos para este producto</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        <a href="inventario.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Inventario
        </a>
    </div>
</section>

<?php
$stmt->close();
$stmt_producto->close();
include '../template/footer.php';
?>
