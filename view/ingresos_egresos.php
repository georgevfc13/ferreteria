<?php
$pageTitle = 'Ingresos y Egresos';
include '../controller/IngresosEgresosController.php';

$datosSemanales = getIngresosEgresosSemanales();
$todos = getAllIngresosEgresos();

// Manejar POST para insertar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $tipo = $_POST['tipo'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    if (insertIngresoEgreso($tipo, $monto, $descripcion, $categoria)) {
        header("Location: ingresos_egresos.php");
        exit();
    }
}

// Manejar POST para eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $codigo_correcto = substr(md5($id), 0, 6); // Código simple basado en ID
    if ($codigo === $codigo_correcto) {
        ocultarIngresoEgreso($id);
        header("Location: ingresos_egresos.php");
        exit();
    }
}

include '../template/header.php';
?>

<main class="container mx-auto px-4 py-8 bg-white bg-opacity-70 rounded-lg shadow-lg mx-4 md:mx-auto my-8">
    <h1 class="text-3xl font-bold text-dark mb-6 text-center">Ingresos y Egresos</h1>

    <!-- Gráfica Detallada -->
    <div class="bg-gray-50 rounded-xl shadow-md p-8 mb-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Reporte Detallado Semanal</h2>
        <canvas id="detailedChart" width="400" height="200"></canvas>
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
                    <?php foreach ($datosSemanales as $dato): ?>
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

    <!-- Formulario para Agregar -->
    <div class="bg-gray-50 rounded-xl shadow-md p-8 mt-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Agregar Nuevo Movimiento</h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                <select name="tipo" id="tipo" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    <option value="ingreso">Ingreso</option>
                    <option value="egreso">Egreso</option>
                </select>
            </div>
            <div>
                <label for="monto" class="block text-sm font-medium text-gray-700">Monto</label>
                <input type="number" step="0.01" name="monto" id="monto" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div class="md:col-span-2">
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900"></textarea>
            </div>
            <div class="md:col-span-2">
                <label for="categoria" class="block text-sm font-medium text-gray-700">Categoría</label>
                <input type="text" name="categoria" id="categoria" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div class="md:col-span-2">
                <button type="submit" name="agregar" class="w-full bg-primary text-secondary py-2 px-4 rounded-md hover:bg-secondary hover:text-primary transition duration-300">Agregar Movimiento</button>
            </div>
        </form>
    </div>

    <!-- Lista de Movimientos con Eliminar -->
    <div class="bg-gray-50 rounded-xl shadow-md p-8 mt-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Lista de Movimientos</h2>
        <div class="space-y-4">
            <?php foreach ($todos as $mov): ?>
                <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow">
                    <div>
                        <p class="font-semibold"><?php echo ucfirst($mov['tipo']); ?>: $<?php echo number_format($mov['monto'], 2); ?> - <?php echo $mov['descripcion']; ?> (<?php echo $mov['categoria']; ?>)</p>
                        <p class="text-sm text-gray-600"><?php echo $mov['fecha']; ?></p>
                    </div>
                    <button onclick="openDeleteModal(<?php echo $mov['id']; ?>, '<?php echo addslashes($mov['descripcion']); ?>')" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-300">Eliminar</button>
                </div>
            <?php endforeach; ?>
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
                    <input type="text" id="codeInput" class="mt-2 px-3 py-2 border border-gray-300 rounded-md w-full">
                </div>
                <div class="flex items-center px-4 py-3">
                    <button id="cancelBtn" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 mr-2">Cancelar</button>
                    <button id="confirmBtn" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="id" id="deleteId">
        <input type="hidden" name="codigo" id="deleteCode">
    </form>

<script>
    function openDeleteModal(id, descripcion) {
        document.getElementById('modalTitle').innerText = `Eliminar: ${descripcion}`;
        const code = id.toString().padStart(6, '0'); // Código simple: ID con ceros
        document.getElementById('confirmationCode').innerText = `Código: ${code}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteId').value = id;
    }

    document.getElementById('cancelBtn').addEventListener('click', function() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('codeInput').value = '';
    });

    document.getElementById('confirmBtn').addEventListener('click', function() {
        const code = document.getElementById('codeInput').value;
        document.getElementById('deleteCode').value = code;
        document.getElementById('deleteForm').submit();
    });

    const ctx = document.getElementById('detailedChart').getContext('2d');
    const detailedChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($datosSemanales, 'dia')); ?>,
            datasets: [{
                label: 'Ingresos',
                data: <?php echo json_encode(array_column($datosSemanales, 'ingresos')); ?>,
                backgroundColor: '#32CD32'
            }, {
                label: 'Gastos',
                data: <?php echo json_encode(array_column($datosSemanales, 'egresos')); ?>,
                backgroundColor: '#FFD700'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
</script>

<?php
include '../template/footer.php';
?>