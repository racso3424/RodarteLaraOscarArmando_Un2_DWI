-- ==============================================================================
-- TechFix - Base de Datos MySQL / MariaDB
-- Esquema y Datos Iniciales (Seed Data)
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `techfix_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `techfix_db`;

-- --------------------------------------------------------
-- 1. Estructura de la tabla `clientes`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(20) NOT NULL,
  `ciudad` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Estructura de la tabla `servicios`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `servicios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cliente_id` INT NOT NULL,
  `equipo` VARCHAR(150) NOT NULL,
  `servicio` TEXT NOT NULL,
  `precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `estado` ENUM('Pendiente', 'En proceso', 'Completado', 'Cancelado') NOT NULL DEFAULT 'Pendiente',
  `fecha_ingreso` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_servicios_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Datos de prueba iniciales (Seed Data)
-- --------------------------------------------------------

INSERT INTO `clientes` (`id`, `nombre`, `email`, `telefono`, `ciudad`) VALUES
(1, 'Carlos Mendoza', 'carlos.mendoza@email.com', '555-0192', 'Ciudad de México'),
(2, 'María Fernández', 'maria.fernandez@email.com', '555-0143', 'Guadalajara'),
(3, 'Roberto Gómez', 'roberto.gomez@email.com', '555-0188', 'Monterrey'),
(4, 'Ana Sofía Torres', 'ana.torres@email.com', '555-0271', 'Puebla');

INSERT INTO `servicios` (`id`, `cliente_id`, `equipo`, `servicio`, `precio`, `estado`, `fecha_ingreso`) VALUES
(101, 1, 'Laptop HP Pavilion', 'Mantenimiento preventivo, limpieza física y formateo con Windows 11', 850.00, 'En proceso', '2026-09-28'),
(102, 2, 'PC Escritorio Ryzen 5', 'Cambio de fuente de poder Certificada 650W 80+ Bronze', 1450.00, 'Completado', '2026-09-25'),
(103, 3, 'Impresora Epson L3150', 'Limpieza profunda de cabezales y purgado de mangueras de tinta', 450.00, 'Pendiente', '2026-09-29'),
(104, 4, 'MacBook Pro 13"', 'Reemplazo de batería original y pasta térmica artic mx-4', 2100.00, 'Completado', '2026-10-01'),
(105, 1, 'Monitor Dell 24"', 'Reparación de tarjeta lógica / fuentes de alimentación', 650.00, 'Pendiente', '2026-10-04');
