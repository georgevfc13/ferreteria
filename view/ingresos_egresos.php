<?php
$pageTitle = 'Ingresos y Egresos';
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
                    <tr class="hover:bg-gray-100">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Lunes</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$1200</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$800</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">$400</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Martes</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$1500</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$900</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">$600</td>
                    </tr>
                    <!-- Más filas -->
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    const ctx = document.getElementById('detailedChart').getContext('2d');
    const detailedChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
            datasets: [{
                label: 'Ingresos',
                data: [1200, 1500, 1800, 1400, 1600, 1900, 1700],
                backgroundColor: '#008000'
            }, {
                label: 'Gastos',
                data: [800, 900, 700, 1000, 850, 950, 750],
                backgroundColor: '#FFFF00'
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