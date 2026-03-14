-- MySQL dump 10.13  Distrib 8.4.8, for Linux (x86_64)
--
-- Host: localhost    Database: dental_sistem
-- ------------------------------------------------------
-- Server version	8.4.8

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_tratamientos`
--

DROP TABLE IF EXISTS `categorias_tratamientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias_tratamientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo_categoria` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_categoria` (`codigo_categoria`),
  CONSTRAINT `categorias_tratamientos_chk_1` CHECK ((`estado` in (_utf8mb4'Activo',_utf8mb4'Inactivo')))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_tratamientos`
--

LOCK TABLES `categorias_tratamientos` WRITE;
/*!40000 ALTER TABLE `categorias_tratamientos` DISABLE KEYS */;
INSERT INTO `categorias_tratamientos` VALUES (1,'CAT-01','Preventivo',NULL,'Activo','2026-03-10 00:49:26','2026-03-10 00:49:26'),(2,'CAT-02','Restaurador',NULL,'Activo','2026-03-10 00:49:26','2026-03-10 00:49:26'),(3,'CAT-03','Endodoncia',NULL,'Activo','2026-03-10 00:49:26','2026-03-10 00:49:26'),(4,'CAT-04','Ortodoncia',NULL,'Activo','2026-03-10 00:49:26','2026-03-10 00:49:26'),(5,'CAT-05','Cirugía',NULL,'Activo','2026-03-10 00:49:26','2026-03-10 00:49:26'),(6,'CAT-06','Estética',NULL,'Activo','2026-03-10 00:49:26','2026-03-10 00:49:26');
/*!40000 ALTER TABLE `categorias_tratamientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `fecha_cita` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Programada',
  `tipo_cita` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo_consulta` text COLLATE utf8mb4_unicode_ci,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `recordatorio_enviado` tinyint(1) DEFAULT '0',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`),
  CONSTRAINT `citas_chk_1` CHECK ((`estado` in (_utf8mb4'Programada',_utf8mb4'Confirmada',_utf8mb4'En progreso',_utf8mb4'Completada',_utf8mb4'Cancelada',_utf8mb4'No presentado')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas`
--

LOCK TABLES `citas` WRITE;
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
/*!40000 ALTER TABLE `citas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consultas`
--

DROP TABLE IF EXISTS `consultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cita_id` int DEFAULT NULL,
  `paciente_id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `fecha_consulta` date DEFAULT (curdate()),
  `motivo_consulta` text COLLATE utf8mb4_unicode_ci,
  `sintomas` text COLLATE utf8mb4_unicode_ci,
  `diagnostico` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `prescripciones` text COLLATE utf8mb4_unicode_ci,
  `proxima_cita_recomendada` date DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cita_id` (`cita_id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `consultas_ibfk_1` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`),
  CONSTRAINT `consultas_ibfk_2` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `consultas_ibfk_3` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultas`
--

LOCK TABLES `consultas` WRITE;
/*!40000 ALTER TABLE `consultas` DISABLE KEYS */;
INSERT INTO `consultas` VALUES (2,NULL,2,1,'2026-03-10','12312','3123','12','1312','12312',NULL,'2026-03-10 23:00:26'),(3,NULL,2,1,'2026-03-10','asdad','asdsad','asdasd','asdsa','asdasd','2026-03-12','2026-03-10 23:12:24'),(4,NULL,2,1,'2026-03-10','1241','4214','12421','1421','421421','2026-03-12','2026-03-10 23:24:50'),(5,NULL,1,1,'2026-03-11','Dolor de muelas','Caries en la mayoria de dientes','caries en todos los dientes','Se le extajeron muchos dientes','Tomar acetaninofen y lavarse todos los dias 3 veces los dientes','2026-03-11','2026-03-11 00:31:37');
/*!40000 ALTER TABLE `consultas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `fecha_nacimiento` date DEFAULT NULL,
  `numero_licencia` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `especialidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol_id` int NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  `fecha_contratacion` date DEFAULT (curdate()),
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `rol_id` (`rol_id`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `empleados_chk_1` CHECK ((`estado` in (_utf8mb4'Activo',_utf8mb4'Inactivo',_utf8mb4'Suspendido')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_cuotas`
--

DROP TABLE IF EXISTS `factura_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_cuotas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `factura_id` int NOT NULL,
  `numero_cuota` int NOT NULL,
  `total_cuotas` int NOT NULL,
  `monto_programado` decimal(12,2) NOT NULL,
  `monto_abonado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `saldo_cuota` decimal(12,2) GENERATED ALWAYS AS ((`monto_programado` - `monto_abonado`)) STORED,
  `fecha_emision` date DEFAULT (curdate()),
  `fecha_vencimiento` date NOT NULL,
  `fecha_pago` timestamp NULL DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `dias_retraso` int DEFAULT '0',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_factura_cuotas_factura_id` (`factura_id`),
  KEY `idx_factura_cuotas_vencimiento_estado` (`fecha_vencimiento`,`estado`),
  KEY `idx_factura_cuotas_estado` (`estado`),
  CONSTRAINT `factura_cuotas_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `factura_cuotas_chk_1` CHECK ((`estado` in (_utf8mb4'pendiente',_utf8mb4'pagado_parcial',_utf8mb4'pagado_completo',_utf8mb4'atrasado',_utf8mb4'vencido',_utf8mb4'cancelado')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_cuotas`
--

LOCK TABLES `factura_cuotas` WRITE;
/*!40000 ALTER TABLE `factura_cuotas` DISABLE KEYS */;
/*!40000 ALTER TABLE `factura_cuotas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_estado_historial`
--

DROP TABLE IF EXISTS `factura_estado_historial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_estado_historial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `factura_id` int NOT NULL,
  `tipo_cambio` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_anterior` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_nuevo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cuota_id` int DEFAULT NULL,
  `cambiado_por` int DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cuota_id` (`cuota_id`),
  KEY `cambiado_por` (`cambiado_por`),
  KEY `idx_factura_historial_factura_id` (`factura_id`),
  KEY `idx_factura_historial_fecha` (`fecha`),
  CONSTRAINT `factura_estado_historial_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `factura_estado_historial_ibfk_2` FOREIGN KEY (`cuota_id`) REFERENCES `factura_cuotas` (`id`),
  CONSTRAINT `factura_estado_historial_ibfk_3` FOREIGN KEY (`cambiado_por`) REFERENCES `empleados` (`id`),
  CONSTRAINT `factura_estado_historial_chk_1` CHECK ((`tipo_cambio` in (_utf8mb4'estado_general',_utf8mb4'estado_pago',_utf8mb4'estado_cuota',_utf8mb4'observaciones')))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_estado_historial`
--

LOCK TABLES `factura_estado_historial` WRITE;
/*!40000 ALTER TABLE `factura_estado_historial` DISABLE KEYS */;
INSERT INTO `factura_estado_historial` VALUES (1,1,'estado_general',NULL,'emitida',NULL,1,'2026-03-12 21:28:01','Emisión inicial','172.18.0.1');
/*!40000 ALTER TABLE `factura_estado_historial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_items`
--

DROP TABLE IF EXISTS `factura_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `factura_id` int NOT NULL,
  `tipo_item` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT '1.00',
  `precio_unitario` decimal(12,2) NOT NULL,
  `descuento_item` decimal(12,2) DEFAULT '0.00',
  `total_item` decimal(12,2) NOT NULL,
  `tratamiento_id` int DEFAULT NULL,
  `tratamiento_aplicado_id` int DEFAULT NULL,
  `consulta_id` int DEFAULT NULL,
  `nota` text COLLATE utf8mb4_unicode_ci,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tratamiento_aplicado_id` (`tratamiento_aplicado_id`),
  KEY `idx_factura_items_factura_id` (`factura_id`),
  KEY `idx_factura_items_tratamiento_id` (`tratamiento_id`),
  KEY `idx_factura_items_consulta_id` (`consulta_id`),
  CONSTRAINT `factura_items_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `factura_items_ibfk_2` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamientos` (`id`),
  CONSTRAINT `factura_items_ibfk_3` FOREIGN KEY (`tratamiento_aplicado_id`) REFERENCES `tratamientos_aplicados` (`id`) ON DELETE SET NULL,
  CONSTRAINT `factura_items_ibfk_4` FOREIGN KEY (`consulta_id`) REFERENCES `consultas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `factura_items_chk_1` CHECK ((`tipo_item` in (_utf8mb4'tratamiento',_utf8mb4'consulta',_utf8mb4'material',_utf8mb4'medicamento',_utf8mb4'honorarios',_utf8mb4'otros')))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_items`
--

LOCK TABLES `factura_items` WRITE;
/*!40000 ALTER TABLE `factura_items` DISABLE KEYS */;
INSERT INTO `factura_items` VALUES (1,1,'tratamiento','asdasdasdasdas (18, 15, 21, 22, 23, 26, 48, 31, 34, 35, 36, 37, 17, 47)',1.00,85.00,0.00,85.00,5,NULL,NULL,NULL,'2026-03-12 21:28:01'),(2,1,'tratamiento','asdasdasdasdasdas (18, 15, 21, 22, 23, 26, 48, 31, 34, 35, 36, 37, 17, 47)',1.00,85.00,0.00,85.00,7,NULL,NULL,NULL,'2026-03-12 21:28:01');
/*!40000 ALTER TABLE `factura_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facturas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paciente_id` int NOT NULL,
  `empleado_id` int DEFAULT NULL,
  `cita_id` int DEFAULT NULL,
  `fecha_emision` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_vencimiento` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `impuestos` decimal(12,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `saldo_pendiente` decimal(12,2) NOT NULL DEFAULT '0.00',
  `estado_general` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
  `estado_pago` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `tipo_factura` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contado',
  `moneda` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT 'USD',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `terminos_condiciones` text COLLATE utf8mb4_unicode_ci,
  `creado_por` int DEFAULT NULL,
  `actualizado_por` int DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`numero`),
  KEY `empleado_id` (`empleado_id`),
  KEY `creado_por` (`creado_por`),
  KEY `actualizado_por` (`actualizado_por`),
  KEY `idx_facturas_paciente_estado` (`paciente_id`,`estado_pago`),
  KEY `idx_facturas_fecha_estado` (`fecha_emision`,`estado_pago`),
  KEY `idx_facturas_numero` (`numero`),
  KEY `idx_facturas_cita_id` (`cita_id`),
  KEY `idx_facturas_estado_pago` (`estado_pago`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE SET NULL,
  CONSTRAINT `facturas_ibfk_3` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `facturas_ibfk_4` FOREIGN KEY (`creado_por`) REFERENCES `empleados` (`id`),
  CONSTRAINT `facturas_ibfk_5` FOREIGN KEY (`actualizado_por`) REFERENCES `empleados` (`id`),
  CONSTRAINT `facturas_chk_1` CHECK ((`estado_general` in (_utf8mb4'borrador',_utf8mb4'emitida',_utf8mb4'anulada'))),
  CONSTRAINT `facturas_chk_2` CHECK ((`estado_pago` in (_utf8mb4'pendiente',_utf8mb4'parcial',_utf8mb4'pagado',_utf8mb4'atrasado',_utf8mb4'vencido'))),
  CONSTRAINT `facturas_chk_3` CHECK ((`tipo_factura` in (_utf8mb4'contado',_utf8mb4'cuotas')))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES (1,'FAC-00001',1,1,NULL,'2026-03-12 00:00:00',NULL,170.00,0.00,0.00,170.00,145.00,'emitida','parcial','contado','USD','asdasdasdasdas',NULL,NULL,NULL,'2026-03-12 21:28:01','2026-03-12 21:28:01');
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_dental`
--

DROP TABLE IF EXISTS `historial_dental`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_dental` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `diente` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condicion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tratamiento_aplicado_id` int DEFAULT NULL,
  `fecha_diagnostico` date NOT NULL,
  `fecha_tratamiento` date DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `registrado_por` int NOT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `tratamiento_aplicado_id` (`tratamiento_aplicado_id`),
  KEY `registrado_por` (`registrado_por`),
  CONSTRAINT `historial_dental_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `historial_dental_ibfk_2` FOREIGN KEY (`tratamiento_aplicado_id`) REFERENCES `tratamientos_aplicados` (`id`),
  CONSTRAINT `historial_dental_ibfk_3` FOREIGN KEY (`registrado_por`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_dental`
--

LOCK TABLES `historial_dental` WRITE;
/*!40000 ALTER TABLE `historial_dental` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_dental` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_sistema`
--

DROP TABLE IF EXISTS `logs_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs_sistema` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `accion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla_afectada` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registro_id` int DEFAULT NULL,
  `valores_anteriores` json DEFAULT NULL,
  `valores_nuevos` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `logs_sistema_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_sistema`
--

LOCK TABLES `logs_sistema` WRITE;
/*!40000 ALTER TABLE `logs_sistema` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_sistema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int DEFAULT NULL,
  `empleado_id` int DEFAULT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente',
  `medio_envio` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intento_envio` int DEFAULT '0',
  `fecha_envio` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `notificaciones_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`),
  CONSTRAINT `notificaciones_chk_1` CHECK ((`estado` in (_utf8mb4'Pendiente',_utf8mb4'Enviado',_utf8mb4'Fallido',_utf8mb4'Leído')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `odontogramas`
--

DROP TABLE IF EXISTS `odontogramas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `odontogramas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `estado_dientes` json DEFAULT NULL,
  `observaciones_generales` text COLLATE utf8mb4_unicode_ci,
  `actualizado_por` int DEFAULT NULL,
  `ultima_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paciente_id` (`paciente_id`),
  KEY `actualizado_por` (`actualizado_por`),
  KEY `idx_odontogramas_paciente` (`paciente_id`),
  CONSTRAINT `odontogramas_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `odontogramas_ibfk_2` FOREIGN KEY (`actualizado_por`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `odontogramas`
--

LOCK TABLES `odontogramas` WRITE;
/*!40000 ALTER TABLE `odontogramas` DISABLE KEYS */;
INSERT INTO `odontogramas` VALUES (1,2,'{\"operatoria\": {\"13\": [\"sano\", \"caries\", \"sano\", \"sano\", \"sano\"], \"14\": [\"sano\", \"sano\", \"sano\", \"sano\", \"caries\"], \"16\": [\"caries\", \"sano\", \"sano\", \"sano\", \"sano\"], \"21\": [\"sano\", \"sano\", \"caries\", \"sano\", \"sano\"], \"22\": [\"sano\", \"sano\", \"sano\", \"sano\", \"caries\"], \"44\": [\"sano\", \"sano\", \"sano\", \"sano\", \"caries\"], \"45\": [\"sano\", \"sano\", \"sano\", \"caries\", \"caries\"], \"46\": [\"caries\", \"sano\", \"sano\", \"sano\", \"sano\"]}, \"diagnostico\": {\"17\": \"ausente\", \"18\": \"restaurado\", \"21\": \"extraccion\", \"47\": \"caries\"}, \"detalles_extra\": {\"prot_guia\": \"12\", \"prot_color\": \"rojo\", \"prot_molde\": \"1\", \"endo_diente\": \"123\", \"endo_trabajo\": \"414\", \"prot_acrilico\": true, \"endo_vitalidad\": \"123\", \"prot_porcelana\": false, \"endo_provisional\": \"1414\"}}','Actualizado desde sistema',NULL,'2026-03-10 23:24:50'),(2,1,'{\"operatoria\": {\"17\": [\"sano\", \"sano\", \"sano\", \"caries\", \"sano\"], \"18\": [\"sano\", \"sano\", \"sano\", \"sano\", \"caries\"], \"47\": [\"caries\", \"sano\", \"sano\", \"sano\", \"sano\"]}, \"diagnostico\": {\"15\": \"restaurado\", \"18\": \"extraccion\", \"21\": \"caries\", \"22\": \"caries\", \"23\": \"restaurado\", \"24\": \"ausente\", \"25\": \"ausente\", \"26\": \"caries\", \"31\": \"caries\", \"32\": \"ausente\", \"34\": \"caries\", \"35\": \"caries\", \"36\": \"extraccion\", \"37\": \"extraccion\", \"44\": \"ausente\", \"46\": \"ausente\", \"47\": \"ausente\", \"48\": \"caries\"}, \"detalles_extra\": {\"prot_guia\": \"123\", \"prot_color\": \"rojo\", \"prot_molde\": \"1231\", \"endo_diente\": \"123\", \"endo_trabajo\": \"414\", \"prot_acrilico\": true, \"endo_vitalidad\": \"12\", \"prot_porcelana\": false, \"endo_provisional\": \"1414\"}}','Actualizado desde sistema',NULL,'2026-03-11 00:31:37');
/*!40000 ALTER TABLE `odontogramas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pacientes`
--

DROP TABLE IF EXISTS `pacientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pacientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_expediente` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono_emergencia` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grupo_sanguineo` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `ciudad` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_postal` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alergias` text COLLATE utf8mb4_unicode_ci,
  `enfermedades_cronicas` text COLLATE utf8mb4_unicode_ci,
  `medicamentos_actuales` text COLLATE utf8mb4_unicode_ci,
  `seguro_medico` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_emergencia_nombre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_emergencia_telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas_medicas` text COLLATE utf8mb4_unicode_ci,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  `preferencia_contacto` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es_menor` tinyint(1) DEFAULT '0',
  `responsable_legal` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DUI` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creado_por` int DEFAULT NULL,
  `actualizado_por` int DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_expediente` (`numero_expediente`),
  KEY `creado_por` (`creado_por`),
  KEY `actualizado_por` (`actualizado_por`),
  CONSTRAINT `pacientes_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `empleados` (`id`),
  CONSTRAINT `pacientes_ibfk_2` FOREIGN KEY (`actualizado_por`) REFERENCES `empleados` (`id`),
  CONSTRAINT `pacientes_chk_1` CHECK ((`genero` in (_utf8mb4'Masculino',_utf8mb4'Femenino',_utf8mb4'Otro'))),
  CONSTRAINT `pacientes_chk_2` CHECK ((`estado` in (_utf8mb4'Activo',_utf8mb4'Inactivo'))),
  CONSTRAINT `pacientes_chk_3` CHECK ((`preferencia_contacto` in (_utf8mb4'telefono',_utf8mb4'email',_utf8mb4'sms',_utf8mb4'whatsapp')))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pacientes`
--

LOCK TABLES `pacientes` WRITE;
/*!40000 ALTER TABLE `pacientes` DISABLE KEYS */;
INSERT INTO `pacientes` VALUES (1,'EXP-001','Juan','Pérez','nelson14pineda@gmail.com','7777-1234',NULL,'2000-01-12','Masculino','O-','Calle los pirineos, colonia montebello.','San Salvador','1101','Acetaninofen','dsadasd','sadasds','asdasa','Jaimingo Lopez','87457854','adasdsasda','Activo','WhatsApp',1,'cristiano ronaldo','USA','066545-7',NULL,NULL,'2026-03-05 23:22:28','2026-03-11 00:52:50'),(2,'2024-002','asdasd','asdasdas','nelson14pineda@gmail.com','78555268',NULL,'1999-01-14','Masculino',NULL,'dasdasdasdad','San Salvador','1011','11111111111111111111111111111','33333','4444','222222','Raulito Usulutan','66457854','555555','Activo',NULL,0,NULL,NULL,NULL,NULL,NULL,'2026-03-09 23:11:07','2026-03-11 00:41:10'),(3,'2024-003','asdasda','asdasd','nel_jhon26','78555258',NULL,'2006-01-04','Masculino',NULL,'sdasdasdsa','San Salvador','1101','dsaad',NULL,NULL,NULL,NULL,NULL,NULL,'Inactivo',NULL,0,NULL,NULL,NULL,NULL,NULL,'2026-03-09 23:11:39','2026-03-10 00:01:07');
/*!40000 ALTER TABLE `pacientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `factura_id` int NOT NULL,
  `cuota_id` int DEFAULT NULL,
  `empleado_id` int DEFAULT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `monto` decimal(12,2) NOT NULL,
  `metodo_pago` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'confirmado',
  `nota` text COLLATE utf8mb4_unicode_ci,
  `registrado_por` int DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `empleado_id` (`empleado_id`),
  KEY `registrado_por` (`registrado_por`),
  KEY `idx_pagos_factura_id` (`factura_id`),
  KEY `idx_pagos_cuota_id` (`cuota_id`),
  KEY `idx_pagos_fecha_pago` (`fecha_pago`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`cuota_id`) REFERENCES `factura_cuotas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pagos_ibfk_3` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`),
  CONSTRAINT `pagos_ibfk_4` FOREIGN KEY (`registrado_por`) REFERENCES `empleados` (`id`),
  CONSTRAINT `pagos_chk_1` CHECK ((`metodo_pago` in (_utf8mb4'efectivo',_utf8mb4'transferencia',_utf8mb4'tarjeta_credito',_utf8mb4'tarjeta_debito',_utf8mb4'cheque',_utf8mb4'deposito',_utf8mb4'otros'))),
  CONSTRAINT `pagos_chk_2` CHECK ((`estado` in (_utf8mb4'confirmado',_utf8mb4'anulado',_utf8mb4'pendiente_verificacion')))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES (1,1,NULL,1,'2026-03-12 21:28:01','2026-03-12 21:28:01',25.00,'efectivo',NULL,'confirmado',NULL,NULL,'2026-03-12 21:28:01');
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `permisos` json DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  CONSTRAINT `roles_chk_1` CHECK ((`estado` in (_utf8mb4'Activo',_utf8mb4'Inactivo')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tratamientos`
--

DROP TABLE IF EXISTS `tratamientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tratamientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `categoria_id` int DEFAULT NULL,
  `duracion_estimada` int DEFAULT NULL,
  `costo_base` decimal(10,2) NOT NULL,
  `requiere_cita` tinyint(1) NOT NULL DEFAULT '1',
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `tratamientos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_tratamientos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tratamientos_chk_1` CHECK ((`estado` in (_utf8mb4'Activo',_utf8mb4'Inactivo')))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tratamientos`
--

LOCK TABLES `tratamientos` WRITE;
/*!40000 ALTER TABLE `tratamientos` DISABLE KEYS */;
INSERT INTO `tratamientos` VALUES (1,'LD-001','Limpieza dental','limpieza dental que se realiza cada 2 meses.',1,30,25.00,1,'Activo','2026-03-10 00:45:26','2026-03-11 00:32:07'),(2,'LC-001','Limpieza de Caries','Limpieza profunda de caries',2,60,50.00,1,'Activo','2026-03-10 01:32:14','2026-03-10 01:32:14'),(3,'LP-001','Limpieza profunda','Limpieza profunda. suele doler mucho al paciente',1,92,95.00,1,'Activo','2026-03-10 01:44:54','2026-03-10 02:09:17'),(4,'asdasd','asdasdasd','dasdasdasdasdas',2,69,58.00,1,'Inactivo','2026-03-10 01:54:27','2026-03-10 02:12:54'),(5,'asdasdasd','asdasdasdasdas','asdasdasdasda',2,96,85.00,1,'Activo','2026-03-10 02:03:58','2026-03-10 02:03:58'),(6,'asdasasda','asdasdsadasdsadas','412412412421',3,123,85.00,1,'Activo','2026-03-10 02:05:10','2026-03-10 02:05:53'),(7,'asdadasdas','asdasdasdasdasdas','12421412421',2,12314,85.00,1,'Inactivo','2026-03-10 02:10:30','2026-03-10 02:10:48'),(8,'2312321','24142412','412412414',3,60,214.00,1,'Inactivo','2026-03-10 02:12:42','2026-03-10 02:12:49'),(9,'sadadasda','sadasdasdas','sadasdasdasdasdasdas',2,44,34.00,1,'Inactivo','2026-03-10 22:29:50','2026-03-10 22:29:57');
/*!40000 ALTER TABLE `tratamientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tratamientos_aplicados`
--

DROP TABLE IF EXISTS `tratamientos_aplicados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tratamientos_aplicados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `consulta_id` int NOT NULL,
  `tratamiento_id` int NOT NULL,
  `diente` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caras_diente` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `realizado_por` int NOT NULL,
  `fecha_aplicacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `consulta_id` (`consulta_id`),
  KEY `tratamiento_id` (`tratamiento_id`),
  KEY `realizado_por` (`realizado_por`),
  CONSTRAINT `tratamientos_aplicados_ibfk_1` FOREIGN KEY (`consulta_id`) REFERENCES `consultas` (`id`),
  CONSTRAINT `tratamientos_aplicados_ibfk_2` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamientos` (`id`),
  CONSTRAINT `tratamientos_aplicados_ibfk_3` FOREIGN KEY (`realizado_por`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tratamientos_aplicados`
--

LOCK TABLES `tratamientos_aplicados` WRITE;
/*!40000 ALTER TABLE `tratamientos_aplicados` DISABLE KEYS */;
/*!40000 ALTER TABLE `tratamientos_aplicados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vista_cuotas_pendientes`
--

DROP TABLE IF EXISTS `vista_cuotas_pendientes`;
/*!50001 DROP VIEW IF EXISTS `vista_cuotas_pendientes`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_cuotas_pendientes` AS SELECT 
 1 AS `paciente`,
 1 AS `telefono`,
 1 AS `factura`,
 1 AS `numero_cuota`,
 1 AS `monto_programado`,
 1 AS `monto_abonado`,
 1 AS `saldo_cuota`,
 1 AS `fecha_vencimiento`,
 1 AS `estado`,
 1 AS `dias_retraso`,
 1 AS `dias_vencido`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_estado_facturas_paciente`
--

DROP TABLE IF EXISTS `vista_estado_facturas_paciente`;
/*!50001 DROP VIEW IF EXISTS `vista_estado_facturas_paciente`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_estado_facturas_paciente` AS SELECT 
 1 AS `paciente_id`,
 1 AS `numero_expediente`,
 1 AS `paciente`,
 1 AS `factura_id`,
 1 AS `numero_factura`,
 1 AS `fecha_emision`,
 1 AS `total`,
 1 AS `saldo_pendiente`,
 1 AS `estado_pago`,
 1 AS `tipo_factura`,
 1 AS `total_cuotas`,
 1 AS `cuotas_pendientes`,
 1 AS `cuotas_atrasadas`,
 1 AS `cuotas_vencidas`,
 1 AS `proximo_vencimiento`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_resumen_financiero_paciente`
--

DROP TABLE IF EXISTS `vista_resumen_financiero_paciente`;
/*!50001 DROP VIEW IF EXISTS `vista_resumen_financiero_paciente`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_resumen_financiero_paciente` AS SELECT 
 1 AS `paciente_id`,
 1 AS `numero_expediente`,
 1 AS `paciente`,
 1 AS `total_facturas`,
 1 AS `total_facturado`,
 1 AS `total_pendiente`,
 1 AS `total_pagado`,
 1 AS `ultima_factura`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vista_cuotas_pendientes`
--

/*!50001 DROP VIEW IF EXISTS `vista_cuotas_pendientes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_cuotas_pendientes` AS select concat(`p`.`nombre`,' ',`p`.`apellido`) AS `paciente`,`p`.`telefono` AS `telefono`,`f`.`numero` AS `factura`,`fc`.`numero_cuota` AS `numero_cuota`,`fc`.`monto_programado` AS `monto_programado`,`fc`.`monto_abonado` AS `monto_abonado`,`fc`.`saldo_cuota` AS `saldo_cuota`,`fc`.`fecha_vencimiento` AS `fecha_vencimiento`,`fc`.`estado` AS `estado`,`fc`.`dias_retraso` AS `dias_retraso`,(case when (`fc`.`fecha_vencimiento` < curdate()) then (to_days(curdate()) - to_days(`fc`.`fecha_vencimiento`)) else 0 end) AS `dias_vencido` from ((`factura_cuotas` `fc` join `facturas` `f` on((`fc`.`factura_id` = `f`.`id`))) join `pacientes` `p` on((`f`.`paciente_id` = `p`.`id`))) where (`fc`.`estado` in ('pendiente','atrasado','vencido')) order by `fc`.`fecha_vencimiento` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_estado_facturas_paciente`
--

/*!50001 DROP VIEW IF EXISTS `vista_estado_facturas_paciente`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_estado_facturas_paciente` AS select `p`.`id` AS `paciente_id`,`p`.`numero_expediente` AS `numero_expediente`,concat(`p`.`nombre`,' ',`p`.`apellido`) AS `paciente`,`f`.`id` AS `factura_id`,`f`.`numero` AS `numero_factura`,`f`.`fecha_emision` AS `fecha_emision`,`f`.`total` AS `total`,`f`.`saldo_pendiente` AS `saldo_pendiente`,`f`.`estado_pago` AS `estado_pago`,`f`.`tipo_factura` AS `tipo_factura`,count(`fc`.`id`) AS `total_cuotas`,sum((case when (`fc`.`estado` = 'pendiente') then 1 else 0 end)) AS `cuotas_pendientes`,sum((case when (`fc`.`estado` = 'atrasado') then 1 else 0 end)) AS `cuotas_atrasadas`,sum((case when (`fc`.`estado` = 'vencido') then 1 else 0 end)) AS `cuotas_vencidas`,min((case when (`fc`.`estado` in ('pendiente','atrasado')) then `fc`.`fecha_vencimiento` end)) AS `proximo_vencimiento` from ((`facturas` `f` join `pacientes` `p` on((`f`.`paciente_id` = `p`.`id`))) left join `factura_cuotas` `fc` on((`f`.`id` = `fc`.`factura_id`))) group by `p`.`id`,`p`.`numero_expediente`,`p`.`nombre`,`p`.`apellido`,`f`.`id`,`f`.`numero`,`f`.`fecha_emision`,`f`.`total`,`f`.`saldo_pendiente`,`f`.`estado_pago`,`f`.`tipo_factura` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_resumen_financiero_paciente`
--

/*!50001 DROP VIEW IF EXISTS `vista_resumen_financiero_paciente`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_resumen_financiero_paciente` AS select `p`.`id` AS `paciente_id`,`p`.`numero_expediente` AS `numero_expediente`,concat(`p`.`nombre`,' ',`p`.`apellido`) AS `paciente`,count(`f`.`id`) AS `total_facturas`,sum(`f`.`total`) AS `total_facturado`,sum(`f`.`saldo_pendiente`) AS `total_pendiente`,sum((case when (`f`.`estado_pago` = 'pagado') then `f`.`total` else 0 end)) AS `total_pagado`,max(`f`.`fecha_emision`) AS `ultima_factura` from (`facturas` `f` join `pacientes` `p` on((`f`.`paciente_id` = `p`.`id`))) group by `p`.`id`,`p`.`numero_expediente`,`p`.`nombre`,`p`.`apellido` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-12 21:40:07
