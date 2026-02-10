-- ============================================
-- Datos de ejemplo para la tabla ropa
-- Ejecutar después de schema.sql
-- ============================================

USE tienda;

-- Insertar datos de ejemplo
INSERT INTO ropa (prenda, marca, talle, precio, imagen) VALUES
('Buzo', 'Nike', 'L', 850.00, 'buzo-nike.jpg'),
('Remera', 'Adidas', 'M', 450.00, 'remera-adidas.jpg'),
('Pantalón', 'Puma', 'XL', 650.00, 'pantalon-puma.jpg'),
('Campera', 'Nike', 'L', 1200.00, 'campera-nike.jpg'),
('Short', 'Adidas', 'S', 380.00, 'short-adidas.jpg');

-- Verificar inserción
SELECT * FROM ropa;
