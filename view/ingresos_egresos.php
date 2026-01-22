<?php
$pageTitle = 'Ingresos y Egresos';
$pageCSS = ['../css/ingresos_egresos.css'];
include '../controller/IngresosEgresosController.php';

include '../template/header.php';
?>

<main class="container mx-auto px-4 py-8 bg-white bg-opacity-70 rounded-lg shadow-lg mx-4 md:mx-auto my-8">
    <h1 class="text-3xl font-bold text-dark mb-6 text-center">Ingresos y Egresos</h1>

    <!-- Gráfica Detallada -->
    <div id="chartContainer" class="bg-gray-50 rounded-xl shadow-md p-8 mb-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Reporte Detallado Semanal</h2>
        <canvas id="detailedChart" width="400" height="200" data-dias="<?php echo htmlspecialchars(json_encode(array_column($ingresos_egresos_semanales, 'dia'))); ?>" data-ingresos="<?php echo htmlspecialchars(json_encode(array_map('floatval', array_column($ingresos_egresos_semanales, 'ingresos')))); ?>" data-egresos="<?php echo htmlspecialchars(json_encode(array_map('floatval', array_column($ingresos_egresos_semanales, 'egresos')))); ?>"></canvas>
    </div>

    <!-- Tabla de Detalles -->
    <div class="bg-gray-50 rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Detalles por Día</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto bg-white rounded-lg">
                <thead class="bg-primary text-secondary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Día</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ingresos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Gastos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ganancia Neta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($ingresos_egresos_semanales as $dato): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $dato['dia']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?php echo number_format($dato['ingresos'], 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?php echo number_format($dato['egresos'], 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $dato['neto'] >= 0 ? 'text-green-600' : 'text-red-600'; ?>">$<?php echo number_format($dato['neto'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulario para Agregar - CON TABS -->
    <div id="formContainer" class="form-card rounded-xl shadow-2xl p-8 mt-8 relative">
        <!-- Tabs -->
        <div class="flex relative z-10 mb-6">
            <button class="tab-button active flex-1 py-3 px-6 bg-white bg-opacity-20 rounded-lg mr-2 text-white font-semibold transition-all duration-300" onclick="showTab('ingreso')">
                <span class="inline-block mr-2">📈</span>Ingreso (Venta)
            </button>
            <button class="tab-button flex-1 py-3 px-6 bg-white bg-opacity-20 rounded-lg text-white font-semibold transition-all duration-300" onclick="showTab('egreso')">
                <span class="inline-block mr-2">📉</span>Egreso (Compra)
            </button>
        </div>

        <!-- Formulario Ingreso -->
        <div id="tab-ingreso" class="tab-content relative z-10">
            <h2 class="text-2xl font-semibold text-white mb-6 text-center">✨ Agregar Nuevo Ingreso</h2>
            <form method="POST" id="movementFormIngreso" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <input type="hidden" name="tipo" value="ingreso">
                <input type="hidden" name="agregar" value="1">
                
                <div class="group md:col-span-2">
                    <label for="id_producto" class="block text-sm font-medium text-white mb-2 transition-all group-hover:text-yellow-200">
                        <span class="inline-block mr-2">📦</span>Producto
                    </label>
                    <select name="id_producto" id="id_producto" required class="form-input mt-1 block w-full py-3 px-4 border-2 border-white border-opacity-30 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent text-white font-semibold">
                        <option value="" class="text-gray-800">-- Selecciona un producto --</option>
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>" data-precio="<?php echo $prod['precio_venta']; ?>" class="text-gray-800">
                                <?php echo $prod['nombre']; ?> - $<?php echo number_format($prod['precio_venta'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="group">
                    <label for="cantidad" class="block text-sm font-medium text-white mb-2 transition-all group-hover:text-yellow-200">
                        <span class="inline-block mr-2">📊</span>Cantidad
                    </label>
                    <input type="number" step="1" name="cantidad" id="cantidad" required class="form-input mt-1 block w-full py-3 px-4 border-2 border-white border-opacity-30 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent text-white font-semibold placeholder-white placeholder-opacity-50" placeholder="0" min="1">
                </div>
                
                <div class="group">
                    <label for="monto_total" class="block text-sm font-medium text-white mb-2 transition-all group-hover:text-yellow-200">
                        <span class="inline-block mr-2">💰</span>Monto Total
                    </label>
                    <input type="number" step="0.01" name="monto_total" id="monto_total" readonly class="form-input mt-1 block w-full py-3 px-4 border-2 border-white border-opacity-30 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent text-white font-semibold placeholder-white placeholder-opacity-50" placeholder="0.00">
                </div>
                
                <div class="md:col-span-2">
                    <button type="submit" class="submit-btn relative w-full text-white font-bold py-4 px-6 rounded-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <span class="relative z-10 text-lg">🚀 Agregar Ingreso</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Formulario Egreso -->
        <div id="tab-egreso" class="tab-content hidden relative z-10">
            <h2 class="text-2xl font-semibold text-white mb-6 text-center">✨ Agregar Nuevo Egreso</h2>
            <form method="POST" id="movementFormEgreso" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <input type="hidden" name="tipo" value="egreso">
                <input type="hidden" name="agregar" value="1">
                
                <div class="group md:col-span-2">
                    <label for="id_proveedor" class="block text-sm font-medium text-white mb-2 transition-all group-hover:text-yellow-200">
                        <span class="inline-block mr-2">🏭</span>Proveedor
                    </label>
                    <select name="id_proveedor" id="id_proveedor" required class="form-input mt-1 block w-full py-3 px-4 border-2 border-white border-opacity-30 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent text-white font-semibold">
                        <option value="" class="text-gray-800">-- Selecciona un proveedor --</option>
                        <?php foreach ($proveedores as $prov): ?>
                            <option value="<?php echo $prov['id']; ?>" class="text-gray-800">
                                <?php echo $prov['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="group md:col-span-2">
                    <label for="monto_egreso" class="block text-sm font-medium text-white mb-2 transition-all group-hover:text-yellow-200">
                        <span class="inline-block mr-2">💰</span>Monto Pagado
                    </label>
                    <input type="number" step="0.01" name="monto" id="monto_egreso" required class="form-input mt-1 block w-full py-3 px-4 border-2 border-white border-opacity-30 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent text-white font-semibold placeholder-white placeholder-opacity-50" placeholder="0.00" min="0.01">
                </div>
                
                <div class="md:col-span-2 group">
                    <label for="detalle" class="block text-sm font-medium text-white mb-2 transition-all group-hover:text-yellow-200">
                        <span class="inline-block mr-2">📝</span>Detalle (Opcional)
                    </label>
                    <textarea name="detalle" id="detalle" rows="2" class="form-input mt-1 block w-full py-3 px-4 border-2 border-white border-opacity-30 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent text-white font-semibold placeholder-white placeholder-opacity-50" placeholder="Ej: Materiales, Herramientas, etc..."></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <button type="submit" name="agregar" class="submit-btn relative w-full text-white font-bold py-4 px-6 rounded-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <span class="relative z-10 text-lg">🚀 Agregar Egreso</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Movimientos con Eliminar -->
    <div class="bg-gray-50 rounded-xl shadow-md p-8 mt-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Lista de Movimientos</h2>
        <div class="space-y-4">
            <?php if (!empty($ingresos_egresos)): ?>
                <?php foreach ($ingresos_egresos as $mov): ?>
                    <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow">
                        <div>
                            <p class="font-semibold text-dark"><?php echo ucfirst($mov['tipo']); ?>: $<?php echo number_format($mov['monto'], 2); ?> - <?php echo $mov['descripcion']; ?> (<?php echo $mov['categoria']; ?>)</p>
                            <p class="text-sm text-green-800"><?php echo $mov['fecha']; ?></p>
                        </div>
                        <button onclick="openDeleteModal(<?php echo $mov['id']; ?>, '<?php echo addslashes($mov['descripcion']); ?>')" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-300">Eliminar</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-500">No hay movimientos registrados</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Confirmar Eliminación</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">¿Estás seguro de eliminar este movimiento? Escribe el código de confirmación:</p>
                    <p class="text-xs text-gray-400 mt-2" id="confirmationCode"></p>
                    <input type="text" id="codeInput" class="mt-2 px-3 py-2 border border-gray-300 rounded-md w-full text-gray-700">
                </div>
                <div class="flex items-center px-4 py-3">
                    <button id="cancelBtn" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 mr-2">Cancelar</button>
                    <button id="confirmBtn" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="eliminar" value="1">
        <input type="hidden" name="id" id="deleteId">
        <input type="hidden" name="codigo" id="deleteCode">
    </form>
</main>

<?php
include '../template/footer.php';
?>

<script src="../js/ingresos_egresos.js"></script>