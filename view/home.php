<?php
$pageTitle = 'Inicio';
include '../controller/HomeController.php';

// Obtener datos
$ventasDia = getVentasDelDia();
$productosMasVendidos = getProductosMasVendidos();
$productosBajoStock = getProductosBajoStock();
$ingresosNetos = getIngresosNetosSemanales();
$datosGrafica = getDatosGraficaSemanal();

include '../template/header.php';
?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-primary to-secondary bg-opacity-70 text-accent py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">Bienvenido a Ferretería JJ</h1>
        <p class="text-xl md:text-2xl mb-8">Software echo por mi</p>
        <a href="#dashboard" class="bg-accent text-secondary px-8 py-3 rounded-full font-semibold text-lg hover:bg-dark hover:text-accent transition duration-300 transform hover:scale-105">Ver Dashboard</a>
    </div>
</section>

<main id="dashboard" class="container mx-auto px-4 py-12 bg-white bg-opacity-70 rounded-lg shadow-lg mx-4 md:mx-auto my-8">
    <h2 class="text-3xl font-bold text-dark text-center mb-10">Dashboard Principal</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Datos de Venta -->
        <div class="bg-white rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105 fade-in-up">
            <div class="flex items-center mb-4">
                <i class="fas fa-dollar-sign text-secondary text-3xl mr-3"></i>
                <h3 class="text-xl font-semibold text-dark">Ventas del Día</h3>
            </div>
            <p class="text-2xl font-bold text-secondary">$<?php echo number_format($ventasDia['total_ventas'] ?? 0, 2); ?></p>
            <p class="text-gray-600"><?php echo $ventasDia['num_transacciones'] ?? 0; ?> transacciones</p>
        </div>

        <!-- Producto Más Vendido -->
        <div class="bg-white rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105 fade-in-up">
            <div class="flex items-center mb-4">
                <i class="fas fa-star text-primary text-3xl mr-3"></i>
                <h3 class="text-xl font-semibold text-dark">Más Vendido</h3>
            </div>
            <?php if (!empty($productosMasVendidos)): ?>
                <p class="text-lg font-semibold text-dark"><?php echo $productosMasVendidos[0]['nombre']; ?></p>
                <p class="text-gray-600"><?php echo $productosMasVendidos[0]['total_vendido']; ?> unidades</p>
            <?php else: ?>
                <p class="text-gray-600">No hay datos</p>
            <?php endif; ?>
        </div>

        <!-- Productos con Bajo Stock -->
        <div class="bg-white rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105 fade-in-up">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-3xl mr-3"></i>
                <h3 class="text-xl font-semibold text-dark">Bajo Stock</h3>
            </div>
            <ul class="text-sm text-gray-600">
                <?php if (!empty($productosBajoStock)): ?>
                    <?php foreach (array_slice($productosBajoStock, 0, 3) as $producto): ?>
                        <li><?php echo $producto['nombre']; ?>: <?php echo $producto['stock']; ?> unidades</li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No hay productos con bajo stock</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Ingresos Netos -->
        <div class="bg-white rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105 fade-in-up">
            <div class="flex items-center mb-4">
                <i class="fas fa-chart-pie text-primary text-3xl mr-3"></i>
                <h3 class="text-xl font-semibold text-dark">Ingresos Netos</h3>
            </div>
            <p class="text-2xl font-bold text-secondary">$<?php echo number_format($ingresosNetos, 2); ?></p>
            <p class="text-gray-600">Esta semana</p>
        </div>
    </div>

    <!-- Reporte Semanal de Gastos e Ingresos (Gráfica) -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
        <h3 class="text-2xl font-semibold text-dark mb-6 text-center">Reporte Semanal de Gastos e Ingresos</h3>
        <canvas id="weeklyChart" width="400" height="200"></canvas>
    </div>
</main>

<script>
    // Gráfica semanal con Chart.js
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    const weeklyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($datosGrafica['dias']); ?>,
            datasets: [{
                label: 'Ingresos',
                data: <?php echo json_encode($datosGrafica['ingresos']); ?>,
                borderColor: '#32CD32',
                backgroundColor: 'rgba(50, 205, 50, 0.1)',
                tension: 0.4
            }, {
                label: 'Gastos',
                data: <?php echo json_encode($datosGrafica['egresos']); ?>,
                borderColor: '#FFD700',
                backgroundColor: 'rgba(255, 215, 0, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Ingresos vs Gastos Semanales'
                }
            }
        }
    });
</script>

<?php
include '../template/footer.php';
?>