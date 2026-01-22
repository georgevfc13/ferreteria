<?php
// ============================================
// CONTROLLER: Ingresos y Egresos
// Orquesta: entrada del usuario -> model -> vista
// ============================================

// Cargar los modelos
include '../model/IngresoEgresoModel.php';
include '../model/ProductoModel.php';
include '../model/ProveedorModel.php';

// Variables iniciales
$pageTitle = 'Ingresos y Egresos';
$pageCSS = ['../css/ingresos_egresos.css'];
$error = null;
$success = null;

// 1. PROCESAR ENTRADA DEL USUARIO (POST)
// ========================================

// Agregar ingreso/egreso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $tipo = $_POST['tipo'] ?? '';
    
    if ($tipo === 'ingreso') {
        // Procesar ingreso de ventas
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $id_producto = (int)($_POST['id_producto'] ?? 0);
        
        if (!empty($tipo) && $cantidad > 0 && $id_producto > 0) {
            // Obtener el precio del producto
            $productos_temp = getProductos('default');
            $precio_venta = 0;
            $nombre_producto = '';
            
            foreach ($productos_temp as $p) {
                if ($p['id'] == $id_producto) {
                    $precio_venta = $p['precio_venta'];
                    $nombre_producto = $p['nombre'];
                    break;
                }
            }
            
            if ($precio_venta > 0) {
                $monto = $cantidad * $precio_venta;
                $descripcion = $nombre_producto;
                $categoria = $nombre_producto;
                
                if (insertIngresoEgreso($tipo, $monto, $descripcion, $categoria, $id_producto, $cantidad)) {
                    header("Location: ingresos_egresos.php");
                    exit();
                } else {
                    $error = "Error al registrar el ingreso";
                }
            }
        }
    } elseif ($tipo === 'egreso') {
        // Procesar egreso de compra a proveedores
        $monto = (float)($_POST['monto'] ?? 0);
        $id_proveedor = (int)($_POST['id_proveedor'] ?? 0);
        $detalle = $_POST['detalle'] ?? '';
        
        if (!empty($tipo) && $monto > 0 && $id_proveedor > 0) {
            // Obtener el nombre del proveedor
            $proveedores_temp = getProveedores();
            $nombre_proveedor = '';
            
            foreach ($proveedores_temp as $prov) {
                if ($prov['id'] == $id_proveedor) {
                    $nombre_proveedor = $prov['nombre'];
                    break;
                }
            }
            
            if (!empty($nombre_proveedor)) {
                $descripcion = "Compra a " . $nombre_proveedor . (!empty($detalle) ? " - " . $detalle : '');
                $categoria = $nombre_proveedor;
                
                if (insertIngresoEgreso($tipo, $monto, $descripcion, $categoria)) {
                    header("Location: ingresos_egresos.php");
                    exit();
                } else {
                    $error = "Error al registrar el egreso";
                }
            }
        }
    }
}

// Eliminar ingreso/egreso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id = (int)($_POST['id'] ?? 0);
    $codigo = $_POST['codigo'] ?? '';
    $codigo_correcto = str_pad($id, 6, '0', STR_PAD_LEFT);
    
    if ($codigo === $codigo_correcto && ocultarIngresoEgreso($id)) {
        header("Location: ingresos_egresos.php");
        exit();
    } else {
        $error = "Código de confirmación incorrecto";
    }
}

// 2. OBTENER DATOS DEL MODELO
// =============================
$ingresos_egresos = getAllIngresosEgresos();
$ingresos_egresos_semanales = getIngresosEgresosSemanales();
$productos = getProductos();
$proveedores = getProveedores();

// Preparar datos para la gráfica
$datos_grafica = [];
foreach ($ingresos_egresos_semanales as $dia) {
    $datos_grafica[] = [
        'dia' => $dia['dia'],
        'ingresos' => $dia['ingresos'],
        'egresos' => $dia['egresos']
    ];
}

// 3. LA VISTA SE CARGA A CONTINUACIÓN (en el archivo view)
?>