<?php
$pageTitle = 'Kardex de Inventario';
include '../conexion.php';

// Obtener el ID del producto desde la solicitud
$producto_id = isset($_GET['producto_id']) ? intval($_GET['producto_id']) : 0;

if ($producto_id <= 0) {
    die("ID de producto inválido");
}

// Obtener información del producto
$sql_producto = "SELECT nombre, precio_compra, stock FROM productos WHERE id = ? AND activo = 1";
$stmt_producto = $conn->prepare($sql_producto);
$stmt_producto->bind_param("i", $producto_id);
$stmt_producto->execute();
$result_producto = $stmt_producto->get_result();
$producto = $result_producto->fetch_assoc();

if (!$producto) {
    die("Producto no encontrado");
}

// Obtener TODOS los movimientos (entradas desde ingresos_egresos + salidas desde detalle_ventas)
$sql = "
    -- Salidas (Ventas)
    SELECT 
        dv.id,
        v.fecha,
        'Salida' as tipo_movimiento,
        dv.cantidad,
        dv.precio_unitario,
        dv.cantidad * dv.precio_unitario as total_movimiento
    FROM detalle_ventas dv
    JOIN ventas v ON dv.id_venta = v.id
    WHERE dv.id_producto = ?
    
    UNION ALL
    
    -- Entradas (Compras desde ingresos_egresos tipo egreso)
    SELECT 
        ie.id,
        ie.fecha,
        'Entrada' as tipo_movimiento,
        0 as cantidad,
        ie.monto as precio_unitario,
        ie.monto as total_movimiento
    FROM ingresos_egresos ie
    WHERE ie.tipo = 'egreso' 
    AND ie.categoria LIKE '%Proveedor%'
    AND ie.activo = 1
    
    ORDER BY fecha ASC, id ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$result = $stmt->get_result();
$movimientos = $result->fetch_all(MYSQLI_ASSOC);

// Calcular saldo acumulado usando promedio ponderado
// Iniciar con el stock actual y trabajar hacia atrás
$saldo_cantidad = $producto['stock'];
$precio_promedio = $producto['precio_compra'];
$saldo_valor = 0;

// Primero, necesitamos calcular el stock inicial sumando todas las salidas al stock actual
$total_salidas = 0;
foreach ($movimientos as $mov) {
    if ($mov['tipo_movimiento'] == 'Salida') {
        $total_salidas += $mov['cantidad'];
    }
}

// Stock inicial = stock actual + todas las salidas
$stock_inicial = $producto['stock'] + $total_salidas;
$saldo_cantidad = $stock_inicial;
$saldo_valor = $stock_inicial * $precio_promedio;

// Ahora recalculamos cada movimiento
foreach ($movimientos as &$mov) {
    if ($mov['tipo_movimiento'] == 'Entrada') {
        // Entrada: recalcular promedio ponderado
        $cantidad_entrada = $mov['total_movimiento'] / $mov['precio_unitario'];
        
        $saldo_cantidad += $cantidad_entrada;
        $saldo_valor += $mov['total_movimiento'];
        
        if ($saldo_cantidad > 0) {
            $precio_promedio = $saldo_valor / $saldo_cantidad;
        }
    } else {
        // Salida: usar precio promedio actual
        $saldo_cantidad -= $mov['cantidad'];
        $saldo_valor -= ($mov['cantidad'] * $precio_promedio);
        
        // Evitar valores negativos
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
        <h1 class="text-4xl font-bold mb-2 text-black">Kardex de Inventario</h1>
        <p class="text-xl text-gray-600">Producto: <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong></p>
        <p class="text-lg text-gray-500">Stock Actual: <strong class="text-blue-600"><?php echo $producto['stock']; ?> unidades</strong></p>
    </div>

    <?php if (empty($movimientos)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        No hay movimientos registrados para este producto. Las salidas se registran automáticamente cuando realizas ventas en "Ingresos y Egresos".
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Fecha</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Tipo</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Cantidad</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Precio Unitario</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Saldo Cantidad</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Precio Promedio</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Saldo Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($movimientos)) {
                    foreach ($movimientos as $mov) {
                        $clase_tipo = $mov['tipo_movimiento'] === 'Entrada' ? 'text-green-600' : 'text-red-600';
                        $bg_clase = $mov['tipo_movimiento'] === 'Entrada' ? 'bg-green-50' : 'bg-red-50';
                        
                        echo '<tr class="border-b hover:bg-gray-50 ' . $bg_clase . '">'; 
                        echo '<td class="px-6 py-4 text-sm text-black">' . date('d/m/Y H:i', strtotime($mov['fecha'])) . '</td>'; 
                        echo '<td class="px-6 py-4 text-sm ' . $clase_tipo . ' font-bold">' . htmlspecialchars($mov['tipo_movimiento']) . '</td>'; 
                        echo '<td class="px-6 py-4 text-right text-sm text-black">' . number_format($mov['cantidad'], 0) . '</td>'; 
                        echo '<td class="px-6 py-4 text-right text-sm text-black">$' . number_format($mov['precio_unitario'], 2) . '</td>'; 
                        echo '<td class="px-6 py-4 text-right font-bold text-sm text-blue-600">' . number_format($mov['saldo_cantidad'], 0) . '</td>'; 
                        echo '<td class="px-6 py-4 text-right text-sm text-black">$' . number_format($mov['precio_promedio'], 2) . '</td>'; 
                        echo '<td class="px-6 py-4 text-right font-bold text-sm text-green-600">$' . number_format($mov['saldo_valor'], 2) . '</td>'; 
                        echo '</tr>'; 
                    }
                } else {
                    echo '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No hay movimientos para este producto</p>
                          </td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex gap-4">
        <a href="inventario.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Inventario
        </a>
        
        <?php if (!empty($movimientos)): ?>
        <button onclick="window.print()" class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300">
            <i class="fas fa-print mr-2"></i>Imprimir Kardex
        </button>
        <?php endif; ?>
    </div>
</section>

<style>
@media print {
    /* Ocultar elementos de navegación y footer al imprimir */
    header, footer, nav, .no-print {
        display: none !important;
    }
    
    /* Ocultar botones de acción */
    .print-hide {
        display: none !important;
    }
    
    /* Ajustar el contenedor principal */
    body {
        margin: 0;
        padding: 20px;
    }
    
    /* Asegurar que la tabla se vea bien */
    table {
        page-break-inside: auto;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    /* Forzar colores en impresión */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Estilo para el encabezado de impresión */
    .print-header {
        border-bottom: 3px solid #2563eb;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    
    /* Hacer la tabla más compacta en impresión */
    table th, table td {
        padding: 8px 12px !important;
        font-size: 11px !important;
    }
    
    /* Mejorar bordes de tabla */
    table {
        border-collapse: collapse;
        width: 100%;
    }
    
    table th {
        background-color: #2563eb !important;
        color: white !important;
    }
    
    table td {
        border: 1px solid #e5e7eb;
    }
}
</style>

<?php
$stmt->close();
$stmt_producto->close();
include '../template/footer.php';
?>