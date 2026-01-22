<?php
$pageTitle = 'Inventario';
$pageCSS = [];
include '../controller/InventarioController.php';
include '../controller/ProveedoresController.php';

$filtro = $_GET['filtro'] ?? 'default';
$productos = getProductos($filtro);
$proveedores = getProveedores();
$productosBajoStock = getProductosBajoStock(10);

// Manejar POST para insertar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $stock = $_POST['stock'];
    $precio_compra = $_POST['precio_compra'];
    $precio_venta = $_POST['precio_venta'];
    $id_proveedor = $_POST['id_proveedor'];
    if (insertProducto($nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor)) {
        header("Location: inventario.php");
        exit();
    }
}

// Manejar edición de producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $stock = $_POST['stock'];
    $precio_compra = $_POST['precio_compra'];
    $precio_venta = $_POST['precio_venta'];
    $id_proveedor = $_POST['id_proveedor'];
    if (editarProducto($id, $nombre, $descripcion, $stock, $precio_compra, $precio_venta, $id_proveedor)) {
        header("Location: inventario.php");
        exit();
    }
}

// Manejar eliminación de producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    if (ocultarProducto($id)) {
        header("Location: inventario.php");
        exit();
    }
}

include '../template/header.php';
?>

<!-- Notificaciones de Bajo Stock (esquina derecha) -->
<?php if (!empty($productosBajoStock)): ?>
    <div class="fixed top-20 right-4 z-50 space-y-3 max-w-sm">
        <?php foreach ($productosBajoStock as $prod): ?>
            <div class="bg-red-100 border-l-4 border-red-600 p-4 rounded-lg shadow-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3 mt-1"></i>
                    <div>
                        <p class="font-bold text-red-800"><?php echo htmlspecialchars($prod['nombre']); ?></p>
                        <p class="text-red-700 text-sm">Stock bajo: <?php echo $prod['stock']; ?> unidades</p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main class="container mx-auto px-4 py-8 bg-white bg-opacity-70 rounded-lg shadow-lg mx-4 md:mx-auto my-8">
    <h1 class="text-3xl font-bold text-dark mb-6 text-center">Inventario</h1>

    <!-- Filtros -->
    <div class="mb-8 bg-gray-50 rounded-lg p-6">
        <form method="GET" class="flex items-center">
            <label for="filtro" class="block text-lg font-semibold text-dark mr-4">Filtrar Productos:</label>
            <select name="filtro" id="filtro" onchange="this.form.submit()" class="block w-full md:w-1/3 py-3 px-4 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-gray-700">
                <option value="default" <?php echo $filtro == 'default' ? 'selected' : ''; ?>>Por defecto</option>
                <option value="mas-stock" <?php echo $filtro == 'mas-stock' ? 'selected' : ''; ?>>Más Stock</option>
                <option value="menos-stock" <?php echo $filtro == 'menos-stock' ? 'selected' : ''; ?>>Menos Stock</option>
                <option value="mas-vendido" <?php echo $filtro == 'mas-vendido' ? 'selected' : ''; ?>>Más Vendido</option>
                <option value="menos-vendido" <?php echo $filtro == 'menos-vendido' ? 'selected' : ''; ?>>Menos Vendido</option>
                <option value="mayor-ganancia" <?php echo $filtro == 'mayor-ganancia' ? 'selected' : ''; ?>>Mayor Ganancia</option>
            </select>
        </form>
    </div>

    <!-- Formulario para Agregar Producto -->
    <div class="bg-gray-50 rounded-lg p-6 mt-8">
        <h2 class="text-2xl font-semibold text-dark mb-6 text-center">Agregar Nuevo Producto</h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                <input type="number" name="stock" id="stock" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div>
                <label for="precio_compra" class="block text-sm font-medium text-gray-700">Precio Compra</label>
                <input type="number" step="0.01" name="precio_compra" id="precio_compra" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div>
                <label for="precio_venta" class="block text-sm font-medium text-gray-700">Precio Venta</label>
                <input type="number" step="0.01" name="precio_venta" id="precio_venta" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
            </div>
            <div>
                <label for="id_proveedor" class="block text-sm font-medium text-gray-700">Proveedor</label>
                <select name="id_proveedor" id="id_proveedor" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900">
                    <option value="">Seleccionar Proveedor</option>
                    <?php foreach ($proveedores as $prov): ?>
                        <option value="<?php echo $prov['id']; ?>"><?php echo htmlspecialchars($prov['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-gray-900"></textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" name="agregar" class="w-full bg-primary text-secondary py-2 px-4 rounded-md hover:bg-secondary hover:text-primary transition duration-300">Agregar Producto</button>
            </div>
        </form>
    </div>
    <div id="productos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $producto): ?>
                <div class="rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105 producto 
                    <?php echo $producto['stock'] <= 0 ? 'bg-gray-200 opacity-50' : 'bg-gray-50'; ?>"
                    data-stock="<?php echo $producto['stock']; ?>" 
                    data-vendido="<?php echo $producto['vendido'] ?? 0; ?>" 
                    data-ganancia="<?php echo $producto['precio_venta'] - $producto['precio_compra']; ?>">
                    
                    <div class="flex items-center mb-4">
                        <i class="fas fa-hammer text-secondary text-3xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-dark"><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                    </div>
                    
                    <p class="text-gray-700 mb-2"><strong>Stock:</strong> <?php echo $producto['stock']; ?></p>
                    <p class="text-gray-700 mb-2"><strong>Vendidos:</strong> <?php echo $producto['vendido'] ?? 0; ?></p>
                    <p class="text-gray-700 mb-4"><strong>Ganancia:</strong> $<?php echo number_format($producto['precio_venta'] - $producto['precio_compra'], 2); ?></p>
                    
                    <div class="flex gap-2 mb-3">
                        <a href="kardex.php?producto_id=<?php echo $producto['id']; ?>" class="flex-1 bg-primary text-secondary text-center py-2 px-3 rounded font-semibold hover:bg-secondary hover:text-primary transition duration-300 text-sm">
                            <i class="fas fa-chart-line mr-1"></i>Kardex
                        </a>
                        <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="flex-1 bg-blue-600 text-white text-center py-2 px-3 rounded font-semibold hover:bg-blue-700 transition duration-300 text-sm">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        <a href="inventario.php?eliminar=<?php echo $producto['id']; ?>" onclick="return confirm('¿Eliminar este producto?')" class="flex-1 bg-red-600 text-white text-center py-2 px-3 rounded font-semibold hover:bg-red-700 transition duration-300 text-sm">
                            <i class="fas fa-trash mr-1"></i>Eliminar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-600">No hay productos registrados.</p>
        <?php endif; ?>
    </div>
</main>

<?php
include '../template/footer.php';
?>