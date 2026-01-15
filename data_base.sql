-- Base de datos para el software pa la ferreteria
-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS ferreteria;
USE ferreteria;

-- Tabla de proveedores
-- Almacena información de los proveedores de productos
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    contacto VARCHAR(50),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
);

-- Tabla de productos
-- Contiene todos los productos disponibles en la ferretería
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    stock INT NOT NULL DEFAULT 0,
    precio_compra DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    id_proveedor INT,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id)
);

-- Tabla de ventas
-- Registra las ventas realizadas
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    cliente VARCHAR(100) -- Opcional, nombre del cliente
);

-- Tabla de detalle de ventas
-- Detalla los productos vendidos en cada venta
CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT,
    id_producto INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id),
    FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- Tabla de ingresos y egresos
-- Registra todos los movimientos financieros
CREATE TABLE ingresos_egresos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    categoria VARCHAR(50), -- Ej: venta, compra, gasto operativo, etc.
    metodo_pago VARCHAR(50) DEFAULT 'efectivo', -- efectivo, tarjeta, cheque, etc.
    activo TINYINT(1) DEFAULT 1 -- Para ocultar en lugar de eliminar
);

-- Tabla de arqueos de caja
-- Registra los cierre de caja diarios
CREATE TABLE arqueos_caja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    saldo_sistema DECIMAL(10,2) NOT NULL,
    saldo_real DECIMAL(10,2) NOT NULL,
    diferencia DECIMAL(10,2) NOT NULL,
    tipo_diferencia ENUM('faltante', 'sobrante') NOT NULL,
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar datos de ejemplo
-- Proveedores de ejemplo
INSERT INTO proveedores (nombre, contacto, telefono, email) VALUES
('Proveedor A', 'Juan Pérez', '123456789', 'juan@proveedora.com'),
('Proveedor B', 'María García', '987654321', 'maria@proveedorb.com');

-- Productos de ejemplo
INSERT INTO productos (nombre, descripcion, stock, precio_compra, precio_venta, id_proveedor) VALUES
('Martillo', 'Martillo de acero', 50, 10.00, 15.00, 1),
('Tornillos M5', 'Paquete de tornillos', 200, 5.00, 8.00, 1),
('Pintura blanca', 'Galón de pintura', 10, 20.00, 30.00, 2);

-- Ingresos/Egresos de ejemplo
INSERT INTO ingresos_egresos (tipo, monto, descripcion, categoria) VALUES
('ingreso', 1500.00, 'Venta semanal', 'venta'),
('egreso', 800.00, 'Compra de productos', 'compra');