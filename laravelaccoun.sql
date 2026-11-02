-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table laravelaccoun.customer
DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `piutang` decimal(13,2) NOT NULL DEFAULT '0.00',
  `kode_customer` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kota` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provinsi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `termin_hari` smallint unsigned NOT NULL DEFAULT '0',
  `kategori` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `limit_kredit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `akun_piutang` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` tinyint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_kode_customer_unique` (`kode_customer`),
  KEY `customer_akun_piutang_foreign` (`akun_piutang`),
  CONSTRAINT `customer_akun_piutang_foreign` FOREIGN KEY (`akun_piutang`) REFERENCES `kode_akun` (`kode_akun`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.customer: ~0 rows (approximately)

-- Dumping structure for table laravelaccoun.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
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

-- Dumping data for table laravelaccoun.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table laravelaccoun.jurnal
DROP TABLE IF EXISTS `jurnal`;
CREATE TABLE IF NOT EXISTS `jurnal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `jenis_transaksi` enum('Kas','Bank','Memorial') COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_transaksi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lawan` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('Debit','Kredit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(13,2) NOT NULL,
  `id_detail` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jurnal_kode_foreign` (`kode`),
  KEY `jurnal_lawan_foreign` (`lawan`),
  CONSTRAINT `jurnal_kode_foreign` FOREIGN KEY (`kode`) REFERENCES `kode_akun` (`kode_akun`),
  CONSTRAINT `jurnal_lawan_foreign` FOREIGN KEY (`lawan`) REFERENCES `kode_akun` (`kode_akun`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.jurnal: ~2 rows (approximately)
INSERT INTO `jurnal` (`id`, `tanggal`, `jenis_transaksi`, `kode_transaksi`, `keterangan`, `kode`, `lawan`, `tipe`, `nominal`, `id_detail`, `created_at`, `updated_at`) VALUES
	(1, '2025-05-23', 'Kas', 'BKM-25-05-1110-001', 'Kas Awal', '1110', '1210', 'Debit', 15000000.00, 1, '2025-05-22 19:05:51', '2025-05-22 19:05:51'),
	(2, '2025-05-23', 'Kas', 'BKK-25-05-1110-001', 'Seragam Sekolah SMK', '1110', '2100', 'Kredit', 1500000.00, 2, '2025-05-22 19:11:26', '2025-05-22 19:11:26'),
	(3, '2025-05-23', 'Kas', 'BKK-25-05-1110-002', 'Seragam Biru SMP', '1110', '2100', 'Kredit', 1250000.00, 3, '2025-05-22 19:12:18', '2025-05-22 19:12:18'),
	(4, '2025-06-01', 'Kas', 'BKM-25-06-1000-001', 'Saldo Awal', '1000', '1110', 'Debit', 150000.00, 4, '2025-06-01 00:34:48', '2025-06-01 00:35:35');

-- Dumping structure for table laravelaccoun.kode_akun
DROP TABLE IF EXISTS `kode_akun`;
CREATE TABLE IF NOT EXISTS `kode_akun` (
  `kode_akun` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `induk_kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  `tipe` enum('Debit','Kredit') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`kode_akun`),
  KEY `kode_akun_induk_kode_foreign` (`induk_kode`),
  KEY `kode_akun_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `kode_akun_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `kode_akun_induk_kode_foreign` FOREIGN KEY (`induk_kode`) REFERENCES `kode_induk` (`kode_induk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.kode_akun: ~11 rows (approximately)
INSERT INTO `kode_akun` (`kode_akun`, `induk_kode`, `nama`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`, `tipe`) VALUES
	('1000', '1001', 'Kas & Bank', '2025-05-22 18:50:07', '2025-05-22 18:59:48', NULL, NULL, 'Debit'),
	('1110', '1001', 'Kas Kecil', '2025-05-22 18:50:37', '2025-05-22 18:59:56', NULL, NULL, 'Debit'),
	('1120', '1001', 'Kas Besar', '2025-05-22 18:51:29', '2025-05-22 19:00:10', NULL, NULL, 'Debit'),
	('1210', '1001', 'Kas Bank BCA', '2025-05-22 18:52:27', '2025-06-01 02:00:42', NULL, NULL, 'Debit'),
	('1220', '1001', 'Kas Bank Mandiri', '2025-05-22 18:52:48', '2025-05-22 19:00:23', NULL, NULL, 'Debit'),
	('2000', '2001', 'Hutang Dagang', '2025-05-22 19:00:47', '2025-05-22 19:00:47', NULL, NULL, 'Kredit'),
	('2100', '2001', 'Hutang Supplier', '2025-05-22 19:01:15', '2025-05-22 19:01:15', NULL, NULL, 'Kredit'),
	('2200', '2001', 'Hutang Karyawan', '2025-05-22 19:01:31', '2025-05-22 19:01:31', NULL, NULL, 'Kredit'),
	('3000', '3001', 'Modal', '2025-05-22 19:02:03', '2025-05-22 19:02:03', NULL, NULL, 'Kredit'),
	('3100', '3001', 'Modal Usaha', '2025-05-22 19:02:23', '2025-05-22 19:02:23', NULL, NULL, 'Kredit'),
	('320', '1001', 'sdfsdf', '2025-06-01 01:57:12', '2025-06-01 01:57:25', NULL, NULL, 'Debit'),
	('4000', '4001', 'Pendapatan', '2025-05-22 19:03:54', '2025-05-22 19:03:54', NULL, NULL, 'Kredit'),
	('4100', '4001', 'Pendapatan Dagang', '2025-05-22 19:04:21', '2025-05-22 19:04:21', NULL, NULL, 'Kredit');

-- Dumping structure for table laravelaccoun.kode_induk
DROP TABLE IF EXISTS `kode_induk`;
CREATE TABLE IF NOT EXISTS `kode_induk` (
  `kode_induk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`kode_induk`),
  KEY `kode_induk_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `kode_induk_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.kode_induk: ~4 rows (approximately)
INSERT INTO `kode_induk` (`kode_induk`, `nama`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
	('1001', 'Aktiva Dasar', '2025-05-22 18:48:39', '2025-06-01 00:36:27', NULL, NULL),
	('2001', 'Pasiva', '2025-05-22 18:48:58', '2025-05-22 18:48:58', NULL, NULL),
	('3001', 'Modal', '2025-05-22 18:49:07', '2025-05-22 18:49:07', NULL, NULL),
	('4001', 'Pendapatan', '2025-05-22 18:49:24', '2025-05-22 18:49:24', NULL, NULL),
	('5001', 'Beban', '2025-05-22 18:49:32', '2025-05-22 18:49:32', NULL, NULL);

-- Dumping structure for table laravelaccoun.kunci_transaksi
DROP TABLE IF EXISTS `kunci_transaksi`;
CREATE TABLE IF NOT EXISTS `kunci_transaksi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jenis_transaksi` enum('Kas','Bank Memorial') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai_kunci` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.kunci_transaksi: ~6 rows (approximately)
INSERT INTO `kunci_transaksi` (`id`, `jenis_transaksi`, `tanggal_mulai_kunci`, `created_at`, `updated_at`) VALUES
	(1, 'Kas', '2025-05-23', '2025-05-22 18:36:55', '2025-05-22 18:36:55'),
	(2, 'Bank Memorial', '2025-05-23', '2025-05-22 18:36:55', '2025-05-22 18:36:55'),
	(3, 'Kas', '2025-05-23', '2025-05-22 18:38:13', '2025-05-22 18:38:13'),
	(4, 'Bank Memorial', '2025-05-23', '2025-05-22 18:38:13', '2025-05-22 18:38:13'),
	(5, 'Kas', '2025-05-23', '2025-05-22 18:39:11', '2025-05-22 18:39:11'),
	(6, 'Bank Memorial', '2025-05-23', '2025-05-22 18:39:11', '2025-05-22 18:39:11');

-- Dumping structure for table laravelaccoun.memorial
DROP TABLE IF EXISTS `memorial`;
CREATE TABLE IF NOT EXISTS `memorial` (
  `kode_memorial` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `tipe` enum('Masuk','Keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(13,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_memorial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.memorial: ~0 rows (approximately)

-- Dumping structure for table laravelaccoun.memorial_detail
DROP TABLE IF EXISTS `memorial_detail`;
CREATE TABLE IF NOT EXISTS `memorial_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_memorial` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lawan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(13,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memorial_detail_kode_foreign` (`kode`),
  KEY `memorial_detail_lawan_foreign` (`lawan`),
  CONSTRAINT `memorial_detail_kode_foreign` FOREIGN KEY (`kode`) REFERENCES `kode_akun` (`kode_akun`),
  CONSTRAINT `memorial_detail_lawan_foreign` FOREIGN KEY (`lawan`) REFERENCES `kode_akun` (`kode_akun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.memorial_detail: ~0 rows (approximately)

-- Dumping structure for table laravelaccoun.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.migrations: ~0 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_resets_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2022_02_09_063930_create_kode_induk_table', 1),
	(6, '2022_02_09_182412_create_kode_akun_table', 1),
	(7, '2022_02_11_031239_create_kunci_transaksi_table', 1),
	(8, '2022_02_11_211433_create_transaksi_kas_table', 1),
	(9, '2022_02_12_161324_create_transaksi_kas_detail_table', 1),
	(10, '2022_02_12_162427_create_jurnal_table', 1),
	(11, '2022_02_12_162454_create_jurnal_detail_table', 1),
	(12, '2022_02_15_061322_create_transaksi_bank_table', 1),
	(13, '2022_02_15_061647_create_transaksi_bank_detail_table', 1),
	(14, '2022_02_15_064242_update_kode_transaksi_bank_foreign_key_to_jurnal_table', 1),
	(15, '2022_02_18_124804_alter_id_detail_transaksi_on_jurnal_detail_table', 1),
	(16, '2022_02_18_154719_delete_keterangan_transaksi_bank_on_transaksi_kas_and_transaksi_bank_table', 1),
	(17, '2022_02_18_162333_drop_jurnal_and_jurnal_detail_table', 1),
	(18, '2022_02_18_162400_drop_jurnal_table', 1),
	(19, '2022_02_18_164725_create_sequences_table', 1),
	(20, '2022_02_18_170757_create_jurnal_baru_table', 1),
	(21, '2022_02_18_175935_create_memorial_table', 1),
	(22, '2022_02_19_035903_create_memorial_detail_table', 1),
	(23, '2022_02_19_065201_drop_saldo_awal_to_kode_akun_table', 1),
	(24, '2022_02_19_190237_create_user_activity_table', 1),
	(25, '2022_02_20_142331_create_laba_rugi_view', 1),
	(26, '2022_02_21_044608_drop_tipe_at_kode_induk_table', 1),
	(27, '2022_02_21_044818_add_tipe_at_kode_akun_table', 1),
	(28, '2022_02_24_064440_add_role_viewer_on_users_table', 1),
	(29, '2022_06_29_151633_create_customer_table', 1),
	(30, '2022_06_30_100134_create_supplier_table', 1);

-- Dumping structure for table laravelaccoun.password_resets
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.password_resets: ~0 rows (approximately)

-- Dumping structure for table laravelaccoun.personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.personal_access_tokens: ~0 rows (approximately)

-- Dumping structure for table laravelaccoun.sequences
DROP TABLE IF EXISTS `sequences`;
CREATE TABLE IF NOT EXISTS `sequences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` year NOT NULL,
  `bulan` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seq_length` tinyint NOT NULL,
  `seq_no` tinyint NOT NULL,
  `kode_akun` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sequences_kode_akun_foreign` (`kode_akun`),
  CONSTRAINT `sequences_kode_akun_foreign` FOREIGN KEY (`kode_akun`) REFERENCES `kode_akun` (`kode_akun`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.sequences: ~2 rows (approximately)
INSERT INTO `sequences` (`id`, `kode`, `tahun`, `bulan`, `seq_length`, `seq_no`, `kode_akun`, `created_at`, `updated_at`) VALUES
	(3, 'BKM', '2025', '05', 3, 1, '1110', '2025-05-22 19:05:51', '2025-05-22 19:05:51'),
	(4, 'BKK', '2025', '05', 3, 2, '1110', '2025-05-22 19:11:26', '2025-05-22 19:12:18'),
	(5, 'BKM', '2025', '06', 3, 1, '1000', '2025-06-01 00:34:47', '2025-06-01 00:34:47');

-- Dumping structure for table laravelaccoun.supplier
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE IF NOT EXISTS `supplier` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hutang` decimal(13,2) NOT NULL DEFAULT '0.00',
  `kode_supplier` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kota` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provinsi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `termin_hari` smallint unsigned NOT NULL DEFAULT '0',
  `nama_bank` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_rekening` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akun_hutang` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_kode_supplier_unique` (`kode_supplier`),
  KEY `supplier_akun_hutang_foreign` (`akun_hutang`),
  CONSTRAINT `supplier_akun_hutang_foreign` FOREIGN KEY (`akun_hutang`) REFERENCES `kode_akun` (`kode_akun`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.supplier: ~0 rows (approximately)

-- Dumping structure for table laravelaccoun.transaksi_bank
DROP TABLE IF EXISTS `transaksi_bank`;
CREATE TABLE IF NOT EXISTS `transaksi_bank` (
  `kode_transaksi_bank` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `akun_kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('Masuk','Keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(13,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_transaksi_bank`),
  KEY `transaksi_bank_akun_kode_foreign` (`akun_kode`),
  KEY `transaksi_bank_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `transaksi_bank_akun_kode_foreign` FOREIGN KEY (`akun_kode`) REFERENCES `kode_akun` (`kode_akun`),
  CONSTRAINT `transaksi_bank_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.transaksi_bank: ~0 rows (approximately)

-- Dumping structure for table laravelaccoun.transaksi_bank_detail
DROP TABLE IF EXISTS `transaksi_bank_detail`;
CREATE TABLE IF NOT EXISTS `transaksi_bank_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi_bank` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_lawan` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(13,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_bank_detail_kode_transaksi_bank_foreign` (`kode_transaksi_bank`),
  KEY `transaksi_bank_detail_kode_lawan_foreign` (`kode_lawan`),
  CONSTRAINT `transaksi_bank_detail_kode_lawan_foreign` FOREIGN KEY (`kode_lawan`) REFERENCES `kode_akun` (`kode_akun`),
  CONSTRAINT `transaksi_bank_detail_kode_transaksi_bank_foreign` FOREIGN KEY (`kode_transaksi_bank`) REFERENCES `transaksi_bank` (`kode_transaksi_bank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.transaksi_bank_detail: ~0 rows (approximately)

-- Dumping structure for table laravelaccoun.transaksi_kas
DROP TABLE IF EXISTS `transaksi_kas`;
CREATE TABLE IF NOT EXISTS `transaksi_kas` (
  `kode_transaksi_kas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `akun_kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('Masuk','Keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(13,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`kode_transaksi_kas`),
  KEY `transaksi_kas_akun_kode_foreign` (`akun_kode`),
  KEY `transaksi_kas_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `transaksi_kas_akun_kode_foreign` FOREIGN KEY (`akun_kode`) REFERENCES `kode_akun` (`kode_akun`),
  CONSTRAINT `transaksi_kas_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.transaksi_kas: ~2 rows (approximately)
INSERT INTO `transaksi_kas` (`kode_transaksi_kas`, `tanggal`, `akun_kode`, `tipe`, `total`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
	('BKK-25-05-1110-001', '2025-05-23', '1110', 'Keluar', 1500000.00, '2025-05-22 19:11:26', '2025-05-22 19:11:26', NULL, NULL),
	('BKK-25-05-1110-002', '2025-05-23', '1110', 'Keluar', 1250000.00, '2025-05-22 19:12:18', '2025-05-22 19:12:18', NULL, NULL),
	('BKM-25-05-1110-001', '2025-05-23', '1110', 'Masuk', 15000000.00, '2025-05-22 19:05:51', '2025-05-22 19:05:51', NULL, NULL),
	('BKM-25-06-1000-001', '2025-06-01', '1000', 'Masuk', 150000.00, '2025-06-01 00:34:47', '2025-06-01 00:35:35', NULL, NULL);

-- Dumping structure for table laravelaccoun.transaksi_kas_detail
DROP TABLE IF EXISTS `transaksi_kas_detail`;
CREATE TABLE IF NOT EXISTS `transaksi_kas_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi_kas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_lawan` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(13,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_kas_detail_kode_transaksi_kas_foreign` (`kode_transaksi_kas`),
  KEY `transaksi_kas_detail_kode_lawan_foreign` (`kode_lawan`),
  CONSTRAINT `transaksi_kas_detail_kode_lawan_foreign` FOREIGN KEY (`kode_lawan`) REFERENCES `kode_akun` (`kode_akun`),
  CONSTRAINT `transaksi_kas_detail_kode_transaksi_kas_foreign` FOREIGN KEY (`kode_transaksi_kas`) REFERENCES `transaksi_kas` (`kode_transaksi_kas`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.transaksi_kas_detail: ~2 rows (approximately)
INSERT INTO `transaksi_kas_detail` (`id`, `kode_transaksi_kas`, `kode_lawan`, `subtotal`, `keterangan`, `created_at`, `updated_at`) VALUES
	(1, 'BKM-25-05-1110-001', '1210', 15000000.00, 'Kas Awal', '2025-05-22 19:05:51', '2025-05-22 19:05:51'),
	(2, 'BKK-25-05-1110-001', '2100', 1500000.00, 'Seragam Sekolah SMK', '2025-05-22 19:11:26', '2025-05-22 19:11:26'),
	(3, 'BKK-25-05-1110-002', '2100', 1250000.00, 'Seragam Biru SMP', '2025-05-22 19:12:18', '2025-05-22 19:12:18'),
	(4, 'BKM-25-06-1000-001', '1110', 150000.00, 'Saldo Awal', '2025-06-01 00:34:48', '2025-06-01 00:35:35');

-- Dumping structure for table laravelaccoun.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` enum('Administrator','Accounting','Viewer') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` tinyint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.users: ~1 rows (approximately)
-- Dumping structure for table laravelaccoun.user_activity
DROP TABLE IF EXISTS `user_activity`;
CREATE TABLE IF NOT EXISTS `user_activity` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `jenis_transaksi` enum('Kas','Bank','Memorial') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('Insert','Update','Delete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activity_id_user_foreign` (`id_user`),
  CONSTRAINT `user_activity_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelaccoun.user_activity: ~2 rows (approximately)
-- Dumping structure for view laravelaccoun.view_laba_rugi
DROP VIEW IF EXISTS `view_laba_rugi`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_laba_rugi` (
	`bulan` INT(10) NULL,
	`tahun` INT(10) NULL,
	`nominal` DECIMAL(35,2) NULL,
	`kode` VARCHAR(15) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`lawan` VARCHAR(15) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`tipe` ENUM('Debit','Kredit') NOT NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Dumping structure for view laravelaccoun.view_laba_rugi
DROP VIEW IF EXISTS `view_laba_rugi`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_laba_rugi`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_laba_rugi` AS select month(`jurnal`.`tanggal`) AS `bulan`,year(`jurnal`.`tanggal`) AS `tahun`,sum(`jurnal`.`nominal`) AS `nominal`,`jurnal`.`kode` AS `kode`,`jurnal`.`lawan` AS `lawan`,`jurnal`.`tipe` AS `tipe` from `jurnal` where (`jurnal`.`deleted_at` is null) and ((`jurnal`.`kode` like '4%') or (`jurnal`.`kode` like '5%') or (`jurnal`.`kode` like '6%') or (`jurnal`.`lawan` like '4%') or (`jurnal`.`lawan` like '5%') or (`jurnal`.`lawan` like '6%')) group by month(`jurnal`.`tanggal`),year(`jurnal`.`tanggal`),`jurnal`.`kode`,`jurnal`.`lawan`,`jurnal`.`tipe` order by month(`jurnal`.`tanggal`),year(`jurnal`.`tanggal`);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
laravelaccounlaravelaccoun_2laravelaccoun_2laravelaccoun_2laravelaccoun_2laravelaccoun_2laravelaccoun_2
-- MODUL BARU Kepegawaian (migration 2026_11_02_000002): karyawan, shift, jadwal_kerja
CREATE TABLE IF NOT EXISTS `karyawan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_karyawan` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departemen` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `status_karyawan` enum('Tetap','Kontrak','Harian','Magang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tetap',
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `npwp` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_bank` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_rekening` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `karyawan_kode_karyawan_unique` (`kode_karyawan`),
  UNIQUE KEY `karyawan_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `shift` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_shift` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_keluar` time NOT NULL,
  `keterangan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `shift` (`nama_shift`, `jam_masuk`, `jam_keluar`, `keterangan`) VALUES
('Pagi', '07:00:00', '15:00:00', 'Shift pagi'),
('Siang', '15:00:00', '23:00:00', 'Shift siang'),
('Malam', '23:00:00', '07:00:00', 'Shift malam (lintas hari)');

CREATE TABLE IF NOT EXISTS `jadwal_kerja` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `karyawan_id` bigint unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `shift_id` bigint unsigned DEFAULT NULL,
  `status` enum('Terjadwal','Hadir','Izin','Sakit','Cuti','Alpa','Libur') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Terjadwal',
  `keterangan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jadwal_kerja_karyawan_id_tanggal_unique` (`karyawan_id`, `tanggal`),
  KEY `jadwal_kerja_shift_id_foreign` (`shift_id`),
  CONSTRAINT `jadwal_kerja_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`),
  CONSTRAINT `jadwal_kerja_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shift` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
