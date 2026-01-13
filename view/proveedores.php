<?php
$pageTitle = 'Nuestros Proveedores';
include '../template/header.php';
?>

<main class="container mx-auto px-4 py-8 bg-white bg-opacity-70 rounded-lg shadow-lg mx-4 md:mx-auto my-8">
    <h1 class="text-3xl font-bold text-dark mb-6 text-center">Nuestros Proveedores</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Proveedor 1 -->
        <div class="bg-gray-50 rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105">
            <div class="flex items-center mb-4">
                <i class="fas fa-building text-secondary text-3xl mr-3"></i>
                <h2 class="text-xl font-semibold text-dark">Proveedor A</h2>
            </div>
            <p class="text-gray-700 mb-4">Especialistas en herramientas eléctricas.</p>
            <p class="text-gray-700 mb-4"><i class="fas fa-phone mr-2"></i>+1234567890</p>
            <a href="https://wa.me/1234567890?text=Solicito%20más%20productos" class="inline-flex items-center bg-green-500 text-white px-4 py-2 rounded-full font-semibold hover:bg-green-600 transition duration-300">
                <i class="fab fa-whatsapp mr-2"></i>Enviar Mensaje
            </a>
        </div>
        <!-- Más proveedores -->
        <div class="bg-gray-50 rounded-xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:transform hover:scale-105">
            <div class="flex items-center mb-4">
                <i class="fas fa-building text-secondary text-3xl mr-3"></i>
                <h2 class="text-xl font-semibold text-dark">Proveedor B</h2>
            </div>
            <p class="text-gray-700 mb-4">Proveedores de materiales de construcción.</p>
            <p class="text-gray-700 mb-4"><i class="fas fa-phone mr-2"></i>+0987654321</p>
            <a href="https://wa.me/0987654321?text=Solicito%20más%20productos" class="inline-flex items-center bg-green-500 text-white px-4 py-2 rounded-full font-semibold hover:bg-green-600 transition duration-300">
                <i class="fab fa-whatsapp mr-2"></i>Enviar Mensaje
            </a>
        </div>
    </div>
</main>

<?php
include '../template/footer.php';
?>