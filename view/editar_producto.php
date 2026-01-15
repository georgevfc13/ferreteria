<?php
$pageTitle = 'Editar Producto';
include '../conexion.php';
include '../controller/InventarioController.php';
include '../controller/ProveedoresController.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$producto = getProductoById($id);
$proveedores = getProveedores();

if (!$producto) {
    die("Producto no encontrado");
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $stock = intval($_POST['stock']);
    $precio_compra = floatval($_POST['precio_compra']);
    $precio_venta = floatval($_POST['precio_venta']);
    $id_proveedor = $_POST['id_proveedor'];
    
    if (editarProducto($id, $nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor)) {
        header("Location: inventario.php");
        exit();
    }
}

include '../template/header.php';
?>

<section class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-8 text-center">Editar Producto</h1>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label for="id_proveedor" class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                    <select name="id_proveedor" id="id_proveedor" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Sin proveedor</option>
                        <?php foreach ($proveedores as $prov): ?>
                            <option value="<?php echo $prov['id']; ?>" <?php echo $producto['id_proveedor'] == $prov['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($prov['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                    <input type="number" name="stock" id="stock" value="<?php echo $producto['stock']; ?>" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label for="precio_compra" class="block text-sm font-medium text-gray-700 mb-2">Precio Compra</label>
                    <input type="number" step="0.01" name="precio_compra" id="precio_compra" value="<?php echo $producto['precio_compra']; ?>" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label for="precio_venta" class="block text-sm font-medium text-gray-700 mb-2">Precio Venta</label>
                    <input type="number" step="0.01" name="precio_venta" id="precio_venta" value="<?php echo $producto['precio_venta']; ?>" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" name="guardar" class="flex-1 bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition duration-300">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
                <a href="inventario.php" class="flex-1 bg-gray-600 text-white font-bold py-3 rounded-lg text-center hover:bg-gray-700 transition duration-300">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</section>

<?php
include '../template/footer.php';
?>
