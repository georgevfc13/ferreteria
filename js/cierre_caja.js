// JavaScript para página de Cierre de Caja

function mostrarObservaciones(observaciones, fecha) {
    document.getElementById('modalFecha').textContent = fecha;
    document.getElementById('modalContenido').textContent = observaciones;
    document.getElementById('modalObservaciones').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalObservaciones').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal al hacer clic fuera de él
    const modal = document.getElementById('modalObservaciones');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
    }

    // Cerrar modal con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModal();
        }
    });
});
