<?php
$pageTitle = 'Inventario';
include '../template/header.php';
?>

<main class="container mx-auto px-4 py-8 bg-white bg-opacity-70 rounded-lg shadow-lg mx-4 md:mx-auto my-8">
    <h1 class="text-3xl font-bold text-dark mb-6 text-center">Inventario</h1>

    <!-- Filtros -->
    <div class="mb-8 bg-gray-50 rounded-lg p-6">
        <label for="filter" class="block text-lg font-semibold text-dark mb-4">Filtrar Productos:</label>
        <select id="filter" class="block w-full md:w-1/3 py-3 px-4 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-gray-700">
            <option value="default">Por defecto</option>
            <option value="mas-stock">Más Stock</option>
            <option value="menos-stock">Menos Stock</option>
            <option value="mas-vendido">Más Vendido</option>
            <option value="menos-vendido">Menos Vendido</option>
            <option value="mayor-ganancia">Mayor Ganancia</option>
        </select>
    </div>

    <!-- Lista de Productos -->
    <div id="productos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="bg-gray-50 rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105 producto" data-stock="50" data-vendido="120" data-ganancia="200">
            <div class="flex items-center mb-4">
                <i class="fas fa-hammer text-secondary text-3xl mr-3"></i>
                <h2 class="text-xl font-semibold text-dark">Martillo</h2>
            </div>
            <p class="text-gray-700 mb-2"><strong>Stock:</strong> 50</p>
            <p class="text-gray-700 mb-2"><strong>Vendidos:</strong> 120</p>
            <p class="text-gray-700"><strong>Ganancia:</strong> $200</p>
        </div>
        <!-- Más productos -->
        <div class="bg-gray-50 rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105 producto" data-stock="200" data-vendido="80" data-ganancia="150">
            <div class="flex items-center mb-4">
                <i class="fas fa-screwdriver text-secondary text-3xl mr-3"></i>
                <h2 class="text-xl font-semibold text-dark">Tornillos M5</h2>
            </div>
            <p class="text-gray-700 mb-2"><strong>Stock:</strong> 200</p>
            <p class="text-gray-700 mb-2"><strong>Vendidos:</strong> 80</p>
            <p class="text-gray-700"><strong>Ganancia:</strong> $150</p>
        </div>
    </div>
</main>

<script>
    document.getElementById('filter').addEventListener('change', function() {
        const filter = this.value;
        const productos = Array.from(document.querySelectorAll('.producto'));
        
        productos.sort((a, b) => {
            switch(filter) {
                case 'mas-stock':
                    return parseInt(b.dataset.stock) - parseInt(a.dataset.stock);
                case 'menos-stock':
                    return parseInt(a.dataset.stock) - parseInt(b.dataset.stock);
                case 'mas-vendido':
                    return parseInt(b.dataset.vendido) - parseInt(a.dataset.vendido);
                case 'menos-vendido':
                    return parseInt(a.dataset.vendido) - parseInt(b.dataset.vendido);
                case 'mayor-ganancia':
                    return parseInt(b.dataset.ganancia) - parseInt(a.dataset.ganancia);
                default:
                    return 0;
            }
        });
        
        const container = document.getElementById('productos');
        container.innerHTML = '';
        productos.forEach(p => container.appendChild(p));
    });
</script>

<?php
include '../template/footer.php';
?>