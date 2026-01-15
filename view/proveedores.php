<?php
$pageTitle = 'Nuestros Proveedores';
include '../controller/ProveedoresController.php';

$proveedores = getProveedores();

// Manejar POST para insertar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $contacto = $_POST['contacto'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    if (insertProveedor($nombre, $contacto, $telefono, $email, $direccion)) {
        header("Location: proveedores.php");
        exit();
    }
}

// Manejar POST para eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $codigo_correcto = str_pad($id, 6, '0', STR_PAD_LEFT);
    if ($codigo === $codigo_correcto) {
        ocultarProveedor($id);
        header("Location: proveedores.php");
        exit();
    }
}

include '../template/header.php';
?>

<main class="container mx-auto px-4 py-8 bg-white bg-opacity-70 rounded-lg shadow-lg mx-4 md:mx-auto my-8">
    <h1 class="text-3xl font-bold text-dark mb-6 text-center">Nuestros Proveedores</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (!empty($proveedores)): ?>
            <?php foreach ($proveedores as $proveedor): ?>
                <div class="bg-gray-50 rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-building text-secondary text-3xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-dark"><?php echo htmlspecialchars($proveedor['nombre']); ?></h2>
                    </div>
                    <p class="text-gray-700 mb-4"><?php echo htmlspecialchars($proveedor['contacto'] ?? 'Sin contacto'); ?></p>
                    <p class="text-gray-700 mb-4"><i class="fas fa-phone mr-2"></i><?php echo htmlspecialchars($proveedor['telefono'] ?? 'Sin teléfono'); ?></p>
                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $proveedor['telefono']); ?>?text=Solicito%20más%20productos" class="inline-flex items-center bg-green-500 text-white px-4 py-2 rounded-full font-semibold hover:bg-green-600 transition duration-300">
                        <i class="fab fa-whatsapp mr-2"></i>Enviar Mensaje
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-600">No hay proveedores registrados.</p>
        <?php endif; ?>
    </div>

    <!-- Formulario para Agregar Proveedor -->
    <div class="bg-gray-50 rounded-xl shadow-lg p-8 mt-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Agregar Nuevo Proveedor</h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div>
                <label for="contacto" class="block text-sm font-medium text-gray-700">Contacto</label>
                <input type="text" name="contacto" id="contacto" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div class="md:col-span-2">
                <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                <textarea name="direccion" id="direccion" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900"></textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" name="agregar" class="w-full bg-primary text-secondary py-2 px-4 rounded-md hover:bg-secondary hover:text-primary transition duration-300">Agregar Proveedor</button>
            </div>
        </form>
    </div>

    <!-- Lista de Proveedores con Eliminar -->
    <div class="bg-gray-50 rounded-xl shadow-lg p-8 mt-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Lista de Proveedores</h2>
        <div class="space-y-4">
            <?php foreach ($proveedores as $prov): ?>
                <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow">
                    <div>
                        <p class="font-semibold text-dark"><?php echo htmlspecialchars($prov['nombre']); ?> - <?php echo htmlspecialchars($prov['contacto']); ?></p>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($prov['telefono']); ?> | <?php echo htmlspecialchars($prov['email']); ?></p>
                    </div>
                    <button onclick="openDeleteModal(<?php echo $prov['id']; ?>, '<?php echo addslashes($prov['nombre']); ?>')" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-300">Eliminar</button>
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
                    <p class="text-sm text-gray-500">¿Estás seguro de eliminar este proveedor? Escribe el código de confirmación:</p>
                    <p class="text-xs text-gray-400 mt-2" id="confirmationCode"></p>
                    <input type="text" id="codeInput" class="mt-2 px-3 py-2 border border-gray-300 rounded-md w-full text-gray-900">
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

<script>
    function openDeleteModal(id, nombre) {
        document.getElementById('modalTitle').innerText = `Eliminar: ${nombre}`;
        const code = id.toString().padStart(6, '0');
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
</script>

<?php
include '../template/footer.php';
?>