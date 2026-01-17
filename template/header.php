<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferretería - <?php echo $pageTitle ?? 'Inicio'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FFD700',
                        secondary: '#32CD32',
                        accent: '#FFFFFF',
                        dark: '#2D3748'
                    }
                }
            }
        }
    </script>
    <!-- Chart.js para gráficas -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="text-accent">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-secondary to-primary shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo y Nombre -->
                    <div class="flex-shrink-0 flex items-center space-x-2">
                        <img src="../img/logo.png" alt="" class="h-20 w-20">
                        <span class="text-xl font-bold text-accent">Ferretería 2J</span>
                    </div>
                </div>
                <!-- Menú Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="home.php" class="text-accent hover:text-dark px-3 py-2 rounded-md text-sm font-medium transition duration-300 transform hover:scale-105"><i class="fas fa-home mr-1"></i>Inicio</a>
                    <a href="ingresos_egresos.php" class="text-accent hover:text-dark px-3 py-2 rounded-md text-sm font-medium transition duration-300 transform hover:scale-105"><i class="fas fa-chart-line mr-1"></i>Ingresos y Egresos</a>
                    <a href="proveedores.php" class="text-accent hover:text-dark px-3 py-2 rounded-md text-sm font-medium transition duration-300 transform hover:scale-105"><i class="fas fa-truck mr-1"></i>Nuestros Proveedores</a>
                    <a href="inventario.php" class="text-accent hover:text-dark px-3 py-2 rounded-md text-sm font-medium transition duration-300 transform hover:scale-105"><i class="fas fa-boxes mr-1"></i>Inventario</a>
                    <a href="cierre_caja.php" class="text-accent hover:text-dark px-3 py-2 rounded-md text-sm font-medium transition duration-300 transform hover:scale-105"><i class="fas fa-cash-register mr-1"></i>Cierre de Caja</a>
                </div>
                <!-- Menú Hamburguesa -->
                <div class="md:hidden flex items-center">
                    <button id="menu-button" class="text-accent hover:text-dark focus:outline-none focus:text-dark p-2 rounded-md">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Menú Móvil -->
        <div id="mobile-menu" class="md:hidden hidden bg-secondary px-2 pt-2 pb-3 space-y-1 sm:px-3 shadow-inner">
            <a href="home.php" class="text-accent hover:bg-primary hover:text-dark block px-3 py-2 rounded-md text-base font-medium transition duration-300"><i class="fas fa-home mr-2"></i>Inicio</a>
            <a href="ingresos_egresos.php" class="text-accent hover:bg-primary hover:text-dark block px-3 py-2 rounded-md text-base font-medium transition duration-300"><i class="fas fa-chart-line mr-2"></i>Ingresos y Egresos</a>
            <a href="proveedores.php" class="text-accent hover:bg-primary hover:text-dark block px-3 py-2 rounded-md text-base font-medium transition duration-300"><i class="fas fa-truck mr-2"></i>Nuestros Proveedores</a>
            <a href="inventario.php" class="text-accent hover:bg-primary hover:text-dark block px-3 py-2 rounded-md text-base font-medium transition duration-300"><i class="fas fa-boxes mr-2"></i>Inventario</a>
            <a href="cierre_caja.php" class="text-accent hover:bg-primary hover:text-dark block px-3 py-2 rounded-md text-base font-medium transition duration-300"><i class="fas fa-cash-register mr-2"></i>Cierre de Caja</a>
        </div>
    </nav>