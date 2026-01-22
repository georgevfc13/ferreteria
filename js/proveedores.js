// JavaScript para página de Proveedores

function openDeleteModal(id, nombre) {
    document.getElementById('modalTitle').innerText = `Eliminar: ${nombre}`;
    const code = id.toString().padStart(6, '0');
    document.getElementById('confirmationCode').innerText = `Código: ${code}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteId').value = id;
}

document.addEventListener('DOMContentLoaded', function() {
    const cancelBtn = document.getElementById('cancelBtn');
    const confirmBtn = document.getElementById('confirmBtn');
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('codeInput').value = '';
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            const code = document.getElementById('codeInput').value;
            document.getElementById('deleteCode').value = code;
            document.getElementById('deleteForm').submit();
        });
    }
});
