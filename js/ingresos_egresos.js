// JavaScript para la página de Ingresos y Egresos

// Cambiar entre pestañas
function switchTab(tabName) {
    const contents = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-button');
    
    contents.forEach(content => content.classList.add('hidden'));
    buttons.forEach(button => button.classList.remove('active'));
    
    document.getElementById(tabName).classList.remove('hidden');
    event.target.classList.add('active');
}

// Crear efecto de arena cayendo
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

// Crear efecto de avión de papel
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

// Configurar listeners de formularios cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const formIngreso = document.getElementById('movementFormIngreso');
    const formEgreso = document.getElementById('movementFormEgreso');
    
    if (formIngreso) {
        formIngreso.addEventListener('submit', function(e) {
            const tipo = 'ingreso';
            createPaperPlaneEffect();
        });
    }
    
    if (formEgreso) {
        formEgreso.addEventListener('submit', function(e) {
            const tipo = 'egreso';
            createSandEffect();
        });
    }
});

// Mostrar animación si está en sesión
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
