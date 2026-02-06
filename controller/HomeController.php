<?php

// Cargar los modelos
include '../model/VentaModel.php';
include '../model/ProductoModel.php';

// Variables iniciales
$pageTitle = 'Dashboard - Inicio';
$pageCSS = [];
$error = null;

// 1. PROCESAR ENTRADA DEL USUARIO (si la hay)
// =============================================
// (Home generalmente no procesa POST, solo muestra datos)

// 2. OBTENER DATOS DEL MODELO
// =============================
$ventas_del_dia = getVentasDelDia();
$productos_mas_vendidos = getProductosMasVendidos(5);
$productos_bajo_stock = getProductosBajoStock();
$ingresos_netos_semanales = getIngresosNetosSemanales();
$datos_grafica_semanal = getDatosGraficaSemanal();

// 3. LA VISTA SE CARGA A CONTINUACIÓN (en el archivo view)
?>
?>