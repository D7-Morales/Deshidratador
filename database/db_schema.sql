-- =====================================================================
-- SISTEMA DE DESHIDRATACIÓN SOLAR INTELIGENTE
-- TRABAJO DE GRADO - TÉCNICO SUPERIOR EN SISTEMAS INFORMÁTICOS
-- 
-- SCRIPT SQL DE ESTRUCTURA DE LA BASE DE DATOS (MYSQL)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `deshidratadoroficial` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `deshidratadoroficial`;

-- ---------------------------------------------------------------------
-- 1. TABLA: usuarios
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` BIGINT UNSIGNED AUTO_INCREMENT,
  `ci` VARCHAR(20) NOT NULL UNIQUE,
  `nombre_completo` VARCHAR(100) NOT NULL,
  `usuario` VARCHAR(50) NOT NULL UNIQUE,
  `password` CHAR(64) NOT NULL,
  `rol` ENUM('ADMINISTRADOR', 'OPERADOR') NOT NULL,
  `estado` ENUM('ACTIVO', 'INACTIVO') NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. TABLA: sensores
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sensores` (
  `id_sensor` BIGINT UNSIGNED AUTO_INCREMENT,
  `nombre_sensor` VARCHAR(50) NOT NULL,
  `tipo_sensor` VARCHAR(50) NOT NULL,
  `ubicacion` VARCHAR(100) NOT NULL,
  `estado` ENUM('ACTIVO', 'INACTIVO') NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_sensor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. TABLA: lecturas_sensor
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lecturas_sensor` (
  `id_lectura` BIGINT UNSIGNED AUTO_INCREMENT,
  `id_sensor` BIGINT UNSIGNED NOT NULL,
  `temperatura` DECIMAL(5,2) NOT NULL,
  `humedad` DECIMAL(5,2) NOT NULL,
  `presion` DECIMAL(7,2) NOT NULL,
  `fecha_hora` DATETIME NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_lectura`),
  KEY `lecturas_sensor_id_sensor_foreign` (`id_sensor`),
  CONSTRAINT `lecturas_sensor_id_sensor_foreign` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id_sensor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. TABLA: frutas
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `frutas` (
  `id_fruta` BIGINT UNSIGNED AUTO_INCREMENT,
  `nombre_fruta` VARCHAR(50) NOT NULL,
  `descripcion` VARCHAR(200) NOT NULL,
  `temperatura_recomendada` DECIMAL(5,2) NOT NULL,
  `humedad_recomendada` DECIMAL(5,2) NOT NULL,
  `tiempo_estimado_horas` INT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_fruta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. TABLA: procesos_deshidratacion
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `procesos_deshidratacion` (
  `id_proceso` BIGINT UNSIGNED AUTO_INCREMENT,
  `id_fruta` BIGINT UNSIGNED NOT NULL,
  `fecha_inicio` DATETIME NOT NULL,
  `fecha_fin` DATETIME NULL DEFAULT NULL,
  `peso_inicial` DECIMAL(8,2) NOT NULL,
  `peso_final` DECIMAL(8,2) NULL DEFAULT NULL,
  `observaciones` TEXT NULL,
  `estado_proceso` ENUM('EN_PROCESO', 'FINALIZADO') NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_proceso`),
  KEY `procesos_deshidratacion_id_fruta_foreign` (`id_fruta`),
  CONSTRAINT `procesos_deshidratacion_id_fruta_foreign` FOREIGN KEY (`id_fruta`) REFERENCES `frutas` (`id_fruta`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- REGISTRO DE DATOS INICIALES (EQUIVALENTE AL SEEDER)
-- ---------------------------------------------------------------------

-- Usuario administrador inicial (Diego, Contraseña: 12345 hasheada en SHA-256)
INSERT INTO `usuarios` (`ci`, `nombre_completo`, `usuario`, `password`, `rol`, `estado`, `created_at`, `updated_at`) 
VALUES ('12345678', 'Diego Bladimir Morales Pantigozo', 'diego', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'ADMINISTRADOR', 'ACTIVO', NOW(), NOW());

-- Sensor de pruebas inicial
INSERT INTO `sensores` (`nombre_sensor`, `tipo_sensor`, `ubicacion`, `estado`, `created_at`, `updated_at`)
VALUES ('BME280 Cámara Solar 1', 'Temperatura, Humedad y Presión (BME280)', 'Cámara de Deshidratación Principal', 'ACTIVO', NOW(), NOW());

-- Catálogo inicial de frutas
INSERT INTO `frutas` (`nombre_fruta`, `descripcion`, `temperatura_recomendada`, `humedad_recomendada`, `tiempo_estimado_horas`, `created_at`, `updated_at`) VALUES
('Manzana', 'Manzana cortada en rodajas de 5mm para deshidratado crujiente.', 57.50, 20.00, 12, NOW(), NOW()),
('Plátano', 'Plátano en rodajas de 6mm, requiere mayor tiempo por su alto contenido de azúcar.', 62.00, 15.00, 18, NOW(), NOW()),
('Frutilla', 'Frutillas cortadas a la mitad. Deshidratado suave y gomoso.', 55.00, 22.00, 10, NOW(), NOW()),
('Naranja', 'Naranja en rodajas finas con cáscara. Excelente para decoración y repostería.', 60.00, 18.00, 14, NOW(), NOW());
