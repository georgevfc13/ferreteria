<?php
$pageTitle = 'Nuestros Proveedores';
$pageCSS = ['../css/proveedores.css'];
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

<link rel="stylesheet" href="../css/proveedores.css">

<main class="container mx-auto px-4 py-8 bg-white bg-opacity-70 rounded-lg shadow-lg mx-4 md:mx-auto my-8">
    <h1 class="text-3xl font-bold text-dark mb-6 text-center">Nuestros Proveedores</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
        <?php if (!empty($proveedores)): ?>
            <?php foreach ($proveedores as $proveedor): ?>
                <div class="bg-gray-50 rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-building text-secondary text-3xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-dark"><?php echo htmlspecialchars($proveedor['nombre']); ?></h2>
                    </div>
                    <p class="text-gray-700 mb-2"><strong>Contacto:</strong> <?php echo htmlspecialchars($proveedor['contacto'] ?? 'Sin contacto'); ?></p>
                    <p class="text-gray-700 mb-2"><i class="fas fa-phone mr-2"></i><?php echo htmlspecialchars($proveedor['telefono'] ?? 'Sin teléfono'); ?></p>
                    <p class="text-gray-700 mb-4"><i class="fas fa-envelope mr-2"></i><?php echo htmlspecialchars($proveedor['email'] ?? 'Sin email'); ?></p>
                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $proveedor['telefono']); ?>?text=Solicito%20más%20productos" 
                       class="inline-flex items-center bg-green-500 text-white px-4 py-2 rounded-full font-semibold hover:bg-green-600 transition duration-300">
                        <i class="fab fa-whatsapp mr-2"></i>Enviar Mensaje
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 text-lg">No hay proveedores registrados.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Formulario para Agregar Proveedor -->
    <div class="bg-gray-50 rounded-xl shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Agregar Nuevo Proveedor</h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div>
                <label for="contacto" class="block text-sm font-medium text-gray-700 mb-2">Contacto</label>
                <input type="text" name="contacto" id="contacto" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div class="md:col-span-2">
                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                <textarea name="direccion" id="direccion" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900"></textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" name="agregar" class="w-full bg-primary text-secondary py-3 px-4 rounded-md hover:bg-secondary hover:text-primary transition duration-300 font-bold">
                    <i class="fas fa-plus-circle mr-2"></i>Agregar Proveedor
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Proveedores con Eliminar -->
    <div class="bg-gray-50 rounded-xl shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Lista de Proveedores</h2>
        <div class="space-y-4">
            <?php if (!empty($proveedores)): ?>
                <?php foreach ($proveedores as $prov): ?>
                    <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow hover:shadow-md transition duration-300">
                        <div>
                            <p class="font-semibold text-dark"><?php echo htmlspecialchars($prov['nombre']); ?> - <?php echo htmlspecialchars($prov['contacto'] ?? 'Sin contacto'); ?></p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($prov['telefono'] ?? 'N/A'); ?> | 
                                <i class="fas fa-envelope mr-1"></i><?php echo htmlspecialchars($prov['email'] ?? 'N/A'); ?>
                            </p>
                        </div>
                        <button onclick="openDeleteModal(<?php echo $prov['id']; ?>, '<?php echo addslashes($prov['nombre']); ?>')" 
                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-300">
                            <i class="fas fa-trash mr-1"></i>Eliminar
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-600 text-center py-8">No hay proveedores para mostrar</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Modal de Eliminación de Proveedor -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full mx-4">
        <h2 id="modalTitle" class="text-2xl font-bold text-dark mb-4">Eliminar Proveedor</h2>
        <p class="text-gray-700 mb-4">Para confirmar la eliminación, ingresa el código de seguridad:</p>
        
        <div class="bg-yellow-100 border border-yellow-400 rounded p-3 mb-4">
            <p id="confirmationCode" class="font-mono text-center text-lg font-bold text-dark">Código: 000000</p>
        </div>
        
        <div class="mb-6">
            <input type="text" id="codeInput" placeholder="Ingresa el código aquí" 
                   class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
        </div>
        
        <form id="deleteForm" method="POST" class="hidden">
            <input type="hidden" name="eliminar" value="1">
            <input type="hidden" id="deleteId" name="id">
            <input type="hidden" id="deleteCode" name="codigo">
        </form>
        
        <div class="flex gap-4">
            <button id="cancelBtn" type="button" 
                    class="flex-1 bg-gray-300 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-400 transition duration-300 font-semibold">
                Cancelar
            </button>
            <button id="confirmBtn" type="button" 
                    class="flex-1 bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-300 font-semibold">
                Eliminar
            </button>
        </div>
    </div>
</div>

<script src="../js/proveedores.js"></script>

<?php
include '../template/footer.php';
?>