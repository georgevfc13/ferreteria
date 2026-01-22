// JavaScript para la página de Ingresos y Egresos

// ===== FUNCIÓN GLOBAL PARA CAMBIAR PESTAÑAS =====
function showTab(tabName) {
    const tabs = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-button');
    
    // Ocultar todas las pestañas
    tabs.forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remover activo de todos los botones
    buttons.forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Mostrar la pestaña seleccionada
    const targetTab = document.getElementById('tab-' + tabName);
    if (targetTab) {
        targetTab.classList.remove('hidden');
    }
    
    // Marcar el botón como activo
    if (event && event.target) {
        event.target.classList.add('active');
    }
}

// ===== FUNCIÓN GLOBAL PARA ABRIR MODAL DE ELIMINACIÓN =====
function openDeleteModal(id, descripcion) {
    const modalTitle = document.getElementById('modalTitle');
    const confirmationCode = document.getElementById('confirmationCode');
    const deleteModal = document.getElementById('deleteModal');
    const deleteId = document.getElementById('deleteId');
    
    if (modalTitle && confirmationCode && deleteModal && deleteId) {
        modalTitle.innerText = `Eliminar: ${descripcion}`;
        const code = id.toString().padStart(6, '0');
        confirmationCode.innerText = `Código: ${code}`;
        deleteModal.classList.remove('hidden');
        deleteId.value = id;
    }
}

// ===== CREAR EFECTO DE ARENA CAYENDO =====
function createSandEffect() {
    const particleCount = 50;
    for (let i = 0; i < particleCount; i++) {
        setTimeout(() => {
            const particle = document.createElement('div');
            particle.className = 'sand-particle';
            
            const startX = Math.random() * window.innerWidth;
            const startY = -10;
            const endX = startX + (Math.random() - 0.5) * 200;
            const endY = window.innerHeight + 10;
            const fallDistance = endY - startY;
            
            particle.style.left = startX + 'px';
            particle.style.top = startY + 'px';
            particle.style.setProperty('--fall-distance', fallDistance + 'px');
            particle.style.animation = `sandfall ${2 + Math.random() * 1}s linear forwards`;
            
            document.body.appendChild(particle);
            
            setTimeout(() => particle.remove(), 3500);
        }, i * 50);
    }
}

// ===== CREAR EFECTO DE AVIÓN DE PAPEL =====
function createPaperPlaneEffect() {
    const planeCount = 15;
    for (let i = 0; i < planeCount; i++) {
        setTimeout(() => {
            const plane = document.createElement('div');
            plane.className = 'paper-plane';
            plane.innerHTML = '✈️';
            
            const startX = Math.random() * window.innerWidth;
            const startY = Math.random() * window.innerHeight;
            const midX = (Math.random() - 0.5) * 400;
            const midY = (Math.random() - 0.5) * -400;
            const endX = (Math.random() - 0.5) * 600;
            const endY = window.innerHeight + 100;
            
            plane.style.left = startX + 'px';
            plane.style.top = startY + 'px';
            plane.style.setProperty('--mid-x', midX + 'px');
            plane.style.setProperty('--mid-y', midY + 'px');
            plane.style.setProperty('--end-x', (endX - startX) + 'px');
            plane.style.setProperty('--end-y', (endY - startY) + 'px');
            plane.style.animation = `planefly ${2 + Math.random() * 0.5}s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards`;
            
            document.body.appendChild(plane);
            
            setTimeout(() => plane.remove(), 3000);
        }, i * 200);
    }
    
    // Efecto de pulso en la gráfica
    setTimeout(() => {
        const chartContainer = document.getElementById('chartContainer');
        if (chartContainer) {
            chartContainer.classList.add('chart-pulse');
            setTimeout(() => {
                chartContainer.classList.remove('chart-pulse');
            }, 1500);
        }
    }, 1500);
}

// ===== CONFIGURAR CUANDO EL DOM ESTÉ LISTO =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Cargado - Inicializando ingresos y egresos...');
    
    // ===== CONFIGURAR CÁLCULO DE MONTO TOTAL =====
    const productoSelect = document.getElementById('id_producto');
    const cantidadInput = document.getElementById('cantidad');
    const montoTotalInput = document.getElementById('monto_total');
    
    const calcularMonto = function() {
        if (productoSelect && cantidadInput && montoTotalInput) {
            const selectedOption = productoSelect.options[productoSelect.selectedIndex];
            const precio = parseFloat(selectedOption.dataset.precio) || 0;
            const cantidad = parseInt(cantidadInput.value) || 0;
            const total = precio * cantidad;
            
            montoTotalInput.value = total > 0 ? total.toFixed(2) : '0.00';
            console.log('Monto calculado:', montoTotalInput.value);
        }
    };
    
    if (productoSelect) {
        productoSelect.addEventListener('change', calcularMonto);
    }
    
    if (cantidadInput) {
        cantidadInput.addEventListener('input', calcularMonto);
        cantidadInput.addEventListener('change', calcularMonto);
    }
    
    // ===== CONFIGURAR BOTONES DEL MODAL =====
    const cancelBtn = document.getElementById('cancelBtn');
    const confirmBtn = document.getElementById('confirmBtn');
    const deleteModal = document.getElementById('deleteModal');
    const codeInput = document.getElementById('codeInput');
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (deleteModal) {
                deleteModal.classList.add('hidden');
            }
            if (codeInput) {
                codeInput.value = '';
            }
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            const code = codeInput ? codeInput.value : '';
            const deleteCode = document.getElementById('deleteCode');
            const deleteForm = document.getElementById('deleteForm');
            
            if (deleteCode) {
                deleteCode.value = code;
            }
            
            if (deleteForm) {
                deleteForm.submit();
            }
        });
    }
    
    // ===== CONFIGURAR FORMULARIOS PARA ANIMACIONES =====
    const formIngreso = document.getElementById('movementFormIngreso');
    const formEgreso = document.getElementById('movementFormEgreso');
    
    if (formIngreso) {
        formIngreso.addEventListener('submit', function(e) {
            createPaperPlaneEffect();
        });
    }
    
    if (formEgreso) {
        formEgreso.addEventListener('submit', function(e) {
            createSandEffect();
        });
    }
    
    // ===== CARGAR GRÁFICA =====
    const ctx = document.getElementById('detailedChart');
    if (ctx) {
        const diasData = JSON.parse(ctx.getAttribute('data-dias') || '[]');
        const ingresosData = JSON.parse(ctx.getAttribute('data-ingresos') || '[]');
        const egresosData = JSON.parse(ctx.getAttribute('data-egresos') || '[]');
        
        console.log('Datos de gráfica:', { dias: diasData, ingresos: ingresosData, egresos: egresosData });
        
        // Esperar a que Chart.js esté cargado
        setTimeout(function() {
            if (diasData.length > 0 && typeof Chart !== 'undefined') {
                const detailedChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: diasData,
                        datasets: [{
                            label: 'Ingresos',
                            data: ingresosData,
                            backgroundColor: '#32CD32',
                            borderColor: '#228B22',
                            borderWidth: 1
                        }, {
                            label: 'Gastos',
                            data: egresosData,
                            backgroundColor: '#FFD700',
                            borderColor: '#DAA520',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        indexAxis: 'x',
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Gráfica inicializada correctamente');
            } else {
                console.log('No se pudo inicializar la gráfica - Datos faltantes o Chart.js no disponible');
            }
        }, 100);
    }
});

// ===== MOSTRAR ANIMACIÓN SI ESTÁ EN SESIÓN =====
function showAnimationIfNeeded() {
    const animationType = document.body.getAttribute('data-animation');
    if (animationType === 'egreso') {
        createSandEffect();
    } else if (animationType === 'ingreso') {
        createPaperPlaneEffect();
    }
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', showAnimationIfNeeded);
