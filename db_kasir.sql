-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: db_kasir
-- ------------------------------------------------------
-- Server version	8.0.36

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
-- Current Database: `db_kasir`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `db_kasir` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `db_kasir`;

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
-- Table structure for table `detail_transaksis`
--

DROP TABLE IF EXISTS `detail_transaksis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_transaksis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `jumlah` int NOT NULL,
  `satuan_jual` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_transaksis_transaksi_id_foreign` (`transaksi_id`),
  KEY `detail_transaksis_produk_id_foreign` (`produk_id`),
  CONSTRAINT `detail_transaksis_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_transaksis_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_transaksis`
--

LOCK TABLES `detail_transaksis` WRITE;
/*!40000 ALTER TABLE `detail_transaksis` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail_transaksis` ENABLE KEYS */;
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
-- Table structure for table `kategoris`
--

DROP TABLE IF EXISTS `kategoris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategoris` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategoris`
--

LOCK TABLES `kategoris` WRITE;
/*!40000 ALTER TABLE `kategoris` DISABLE KEYS */;
INSERT INTO `kategoris` VALUES (1,'Makanan','Jenis makanan ringan dan berat','2026-08-28 02:48:36','2026-08-28 02:48:36'),(2,'Minuman','Jenis minuman segar dan kemasan','2026-08-28 02:48:36','2026-08-28 02:48:36'),(3,'Sembako','Kebutuhan pokok sehari-hari','2026-08-28 02:48:36','2026-08-28 02:48:36'),(4,'Rokok','Berbagai merk rokok','2026-08-28 02:48:36','2026-08-28 02:48:36'),(5,'Kebutuhan Mandi','Sabun, sampo, dll','2026-08-28 02:48:36','2026-08-28 02:48:36');
/*!40000 ALTER TABLE `kategoris` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_19_074542_create_kategoris_table',1),(5,'2026_08_19_074543_create_produks_table',1),(6,'2026_08_19_074544_create_transaksis_table',1),(7,'2026_08_19_074545_create_detail_transaksis_table',1),(8,'2026_08_22_000001_create_stock_opnames_table',1),(9,'2026_08_22_000002_add_barcode_to_produks_table',1),(10,'2026_08_22_000003_add_harga_grosir_to_produks_table',1),(11,'2026_08_23_000001_add_satuan_to_produks_table',1),(12,'2026_08_23_000001_create_pengaturans_table',1),(13,'2026_08_23_000002_create_satuans_table',1),(14,'2026_08_23_000003_create_produk_satuans_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `pengaturans`
--

DROP TABLE IF EXISTS `pengaturans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengaturans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengaturans_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengaturans`
--

LOCK TABLES `pengaturans` WRITE;
/*!40000 ALTER TABLE `pengaturans` DISABLE KEYS */;
/*!40000 ALTER TABLE `pengaturans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produk_satuans`
--

DROP TABLE IF EXISTS `produk_satuans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produk_satuans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produk_id` bigint unsigned NOT NULL,
  `satuan_id` bigint unsigned NOT NULL,
  `konversi` int NOT NULL DEFAULT '1',
  `harga_jual` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_satuans_produk_id_satuan_id_unique` (`produk_id`,`satuan_id`),
  KEY `produk_satuans_satuan_id_foreign` (`satuan_id`),
  CONSTRAINT `produk_satuans_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `produk_satuans_satuan_id_foreign` FOREIGN KEY (`satuan_id`) REFERENCES `satuans` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produk_satuans`
--

LOCK TABLES `produk_satuans` WRITE;
/*!40000 ALTER TABLE `produk_satuans` DISABLE KEYS */;
/*!40000 ALTER TABLE `produk_satuans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produks`
--

DROP TABLE IF EXISTS `produks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori_id` bigint unsigned NOT NULL,
  `kode_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan_id` bigint unsigned NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `harga_beli` decimal(15,2) NOT NULL DEFAULT '0.00',
  `harga_jual` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produks_kode_produk_unique` (`kode_produk`),
  UNIQUE KEY `produks_barcode_unique` (`barcode`),
  KEY `produks_kategori_id_foreign` (`kategori_id`),
  KEY `produks_satuan_id_foreign` (`satuan_id`),
  CONSTRAINT `produks_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategoris` (`id`) ON DELETE CASCADE,
  CONSTRAINT `produks_satuan_id_foreign` FOREIGN KEY (`satuan_id`) REFERENCES `satuans` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produks`
--

LOCK TABLES `produks` WRITE;
/*!40000 ALTER TABLE `produks` DISABLE KEYS */;
INSERT INTO `produks` VALUES (1,1,'MKN001',NULL,'Indomie Goreng',1,50,2500.00,3500.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(2,1,'MKN002',NULL,'Indomie Kuah Soto',1,40,2500.00,3500.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(3,1,'MKN003',NULL,'Nabati Richeese',1,30,1000.00,1500.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(4,1,'MKN004',NULL,'Chitato Lite',1,20,8000.00,11000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(5,2,'MNM001',NULL,'Aqua 600ml',12,60,3000.00,4000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(6,2,'MNM002',NULL,'Coca Cola 330ml',13,40,5000.00,7000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(7,2,'MNM003',NULL,'Kopi ABC Sachet',14,100,1500.00,2000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(8,2,'MNM004',NULL,'Teh Pucuk 350ml',12,35,3000.00,4000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(9,3,'SMB001',NULL,'Beras 1kg',6,25,12000.00,14000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(10,3,'SMB002',NULL,'Gula Pasir 1kg',6,20,13000.00,15000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(11,3,'SMB003',NULL,'Minyak Goreng 1L',8,15,15000.00,17000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(12,3,'SMB004',NULL,'Telur Ayam 1kg',6,10,25000.00,28000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(13,4,'ROK001',NULL,'Surya 12',1,50,18000.00,20000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(14,4,'ROK002',NULL,'Djarum Super',1,40,20000.00,22000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(15,5,'KBM001',NULL,'Lifebuoy Sabun',1,30,3500.00,5000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(16,5,'KBM002',NULL,'Pantene Sampo Sachet',14,40,1000.00,1500.00,'2026-08-28 02:48:36','2026-08-28 02:48:36'),(17,5,'KBM003',NULL,'Rexona Deo',1,20,12000.00,15000.00,'2026-08-28 02:48:36','2026-08-28 02:48:36');
/*!40000 ALTER TABLE `produks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `satuans`
--

DROP TABLE IF EXISTS `satuans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `satuans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `satuans_nama_satuan_unique` (`nama_satuan`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `satuans`
--

LOCK TABLES `satuans` WRITE;
/*!40000 ALTER TABLE `satuans` DISABLE KEYS */;
INSERT INTO `satuans` VALUES (1,'pcs','2026-08-28 02:48:28','2026-08-28 02:48:28'),(2,'dus','2026-08-28 02:48:28','2026-08-28 02:48:28'),(3,'box','2026-08-28 02:48:28','2026-08-28 02:48:28'),(4,'pack','2026-08-28 02:48:28','2026-08-28 02:48:28'),(5,'renceng','2026-08-28 02:48:28','2026-08-28 02:48:28'),(6,'kg','2026-08-28 02:48:28','2026-08-28 02:48:28'),(7,'gram','2026-08-28 02:48:28','2026-08-28 02:48:28'),(8,'liter','2026-08-28 02:48:29','2026-08-28 02:48:29'),(9,'ml','2026-08-28 02:48:29','2026-08-28 02:48:29'),(10,'meter','2026-08-28 02:48:29','2026-08-28 02:48:29'),(11,'lembar','2026-08-28 02:48:29','2026-08-28 02:48:29'),(12,'botol','2026-08-28 02:48:29','2026-08-28 02:48:29'),(13,'kaleng','2026-08-28 02:48:29','2026-08-28 02:48:29'),(14,'sachet','2026-08-28 02:48:29','2026-08-28 02:48:29'),(15,'unit','2026-08-28 02:48:29','2026-08-28 02:48:29'),(16,'tim','2026-08-28 02:48:29','2026-08-28 02:48:29'),(17,'bks','2026-08-28 02:48:29','2026-08-28 02:48:29');
/*!40000 ALTER TABLE `satuans` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('pPnRsNWI7zpwwYEZN2YyKzZU91LtH4cYdLL4J08Z',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Microsoft Windows 10.0.18363; en-US) PowerShell/7.5.2','YTozOntzOjY6Il90b2tlbiI7czo0MDoidmgxeXZuMHROQ1RXWnJlVm9hc3FMaDliQnZKUlJMWHlDRlpxbDBmYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1787910569),('w0brpQfKdjUaRmFtQVIOZrawkqIxGNYkeMaOl6kX',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoic2RhQW1nRVkzaWVFaU9vUEVscUVyYnViUXVRZDdnckpZaTV3RHdwTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9rYXNpciI7czo1OiJyb3V0ZSI7czoxMToia2FzaXIuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc4NzkxMDc0MDt9fQ==',1787910757);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_opname_details`
--

DROP TABLE IF EXISTS `stock_opname_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_opname_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stock_opname_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `stok_sistem` int NOT NULL,
  `stok_fisik` int NOT NULL,
  `selisih` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_opname_details_stock_opname_id_foreign` (`stock_opname_id`),
  KEY `stock_opname_details_produk_id_foreign` (`produk_id`),
  CONSTRAINT `stock_opname_details_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_opname_details_stock_opname_id_foreign` FOREIGN KEY (`stock_opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_opname_details`
--

LOCK TABLES `stock_opname_details` WRITE;
/*!40000 ALTER TABLE `stock_opname_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_opname_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_opnames`
--

DROP TABLE IF EXISTS `stock_opnames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_opnames` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_opname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_opnames_no_opname_unique` (`no_opname`),
  KEY `stock_opnames_user_id_foreign` (`user_id`),
  CONSTRAINT `stock_opnames_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_opnames`
--

LOCK TABLES `stock_opnames` WRITE;
/*!40000 ALTER TABLE `stock_opnames` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_opnames` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksis`
--

DROP TABLE IF EXISTS `transaksis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaksis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_transaksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `total_harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `bayar` decimal(15,2) NOT NULL DEFAULT '0.00',
  `kembalian` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaksis_no_transaksi_unique` (`no_transaksi`),
  KEY `transaksis_user_id_foreign` (`user_id`),
  CONSTRAINT `transaksis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksis`
--

LOCK TABLES `transaksis` WRITE;
/*!40000 ALTER TABLE `transaksis` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaksis` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@kasir.com',NULL,'$2y$12$7FUDkUbPdsqHyLmyfy9nZeGpXJVDB849e.WGC5P7BwOf1vetWclUq',NULL,'2026-08-28 02:48:36','2026-08-28 02:48:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-28 16:53:03
