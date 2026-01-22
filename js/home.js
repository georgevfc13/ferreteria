// JavaScript para página de Inicio (Home)

document.addEventListener('DOMContentLoaded', function() {
    // Gráfica semanal con Chart.js
    const ctx = document.getElementById('weeklyChart');
    if (ctx) {
        const chartCtx = ctx.getContext('2d');
        
        // Obtener datos del atributo data del canvas
        const diasData = JSON.parse(ctx.getAttribute('data-dias') || '[]');
        const ingresosData = JSON.parse(ctx.getAttribute('data-ingresos') || '[]');
        const egresosData = JSON.parse(ctx.getAttribute('data-egresos') || '[]');
        
        const weeklyChart = new Chart(chartCtx, {
            type: 'line',
            data: {
                labels: diasData,
                datasets: [{
                    label: 'Ingresos',
                    data: ingresosData,
                    borderColor: '#32CD32',
                    backgroundColor: 'rgba(50, 205, 50, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Gastos',
                    data: egresosData,
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
    }
});
