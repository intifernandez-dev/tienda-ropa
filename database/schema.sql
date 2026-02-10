-- ============================================
-- Base de datos: Tienda de Ropa
-- Descripción: Sistema CRUD para gestión de prendas
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS tienda;

-- Usar la base de datos
USE tienda;

-- Eliminar tabla si existe (para reinstalación limpia)
DROP TABLE IF EXISTS ropa;

-- Crear tabla ropa
CREATE TABLE ropa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenda VARCHAR(100) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    talle VARCHAR(10) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255) DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Índices para mejorar rendimiento
CREATE INDEX idx_prenda ON ropa(prenda);
CREATE INDEX idx_marca ON ropa(marca);
CREATE INDEX idx_precio ON ropa(precio);
