-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Nov 14, 2026 at 08:47 AM
-- Server version: 11.4.9-MariaDB
-- PHP Version: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_lab_keperawatan`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_inventaris`
--

DROP TABLE IF EXISTS `tb_inventaris`;
CREATE TABLE IF NOT EXISTS `tb_inventaris` (
  `id_barang` int(11) NOT NULL AUTO_INCREMENT,
  `kode_barcode` varchar(50) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `kategori` varchar(100) DEFAULT 'Lainnya',
  `jenis` enum('Alat','Bahan') NOT NULL DEFAULT 'Alat',
  `stok` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(30) NOT NULL,
  `kondisi` enum('Baik','Rusak Ringan','Rusak Berat') NOT NULL DEFAULT 'Baik',
  `tgl_pengadaan` date NOT NULL,
  `lokasi_penyimpanan` varchar(100) DEFAULT 'Lemari Utama Lab',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_barang`),
  UNIQUE KEY `uk_kode_barcode` (`kode_barcode`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_inventaris`
--

INSERT INTO `tb_inventaris` (`id_barang`, `kode_barcode`, `nama_barang`, `kategori`, `jenis`, `stok`, `satuan`, `kondisi`, `tgl_pengadaan`, `lokasi_penyimpanan`, `created_at`, `updated_at`) VALUES
(15, 'ALT-001', 'Alat Semprot Disinfektan', 'Alat Medis / Instrumen', 'Alat', 2, 'Buah', 'Baik', '0000-00-00', 'Lab Keperawatan', '2026-10-13 07:33:50', '2026-11-14 13:53:51'),
(16, 'ALT-002', 'APD Lengkap', 'Alat Medis / Instrumen', 'Alat', 10, 'Set', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-13 07:38:35', '2026-11-11 17:58:02'),
(17, 'ALT-003', 'Aprone', 'Alat Medis / Instrumen', 'Alat', 14, 'Buah', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-13 07:39:42', '2026-11-11 21:37:07'),
(18, 'ALT-004', 'Baju Pasien', 'Alat Medis / Instrumen', 'Alat', 6, 'Pasang', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-13 07:40:28', '2026-10-13 07:40:28'),
(19, 'ALT-005', 'Bak Instrumen', 'Alat Medis / Instrumen', 'Alat', 4, 'Buah', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-13 07:41:25', '2026-10-13 07:41:25'),
(20, 'ALT-006', 'Bantal Pasien', 'Alat Medis / Instrumen', 'Alat', 6, 'Buah', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-13 07:42:01', '2026-11-07 22:21:59'),
(21, 'ALT-007', 'Baskom Besar Aluminium', 'Alat Medis / Instrumen', 'Alat', 2, 'Buah', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-13 07:42:42', '2026-10-13 07:42:42'),
(22, 'ALT-008', 'Baskom Besar Plastik', 'Alat Medis / Instrumen', 'Alat', 4, 'Buah', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-13 07:43:42', '2026-10-13 07:43:42'),
(23, 'ALT-009', 'Baskom Kecil Aluinium', 'Alat Medis / Instrumen', 'Alat', 3, 'Buah', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-13 07:45:11', '2026-10-26 15:09:56'),
(24, 'ALT-010', 'Baskom Kecil Plastik', 'Alat Medis / Instrumen', 'Alat', 12, 'Buah', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-13 07:46:07', '2026-10-13 07:46:07'),
(25, 'ALT-011', 'Baskom Sedang Aluminium', 'Alat Medis / Instrumen', 'Alat', 16, 'Buah', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-13 07:46:44', '2026-10-26 15:07:31'),
(27, 'BHP-20261024-444', 'Alkohol Swab', 'Bahan Habis Pakai Medis', 'Bahan', 7, 'Box', 'Baik', '0000-00-00', 'Ruang Penyimpanan Keperawatan', '2026-10-24 14:11:48', '2026-11-08 10:11:07'),
(29, 'BHP-20261109-425', 'Tissue Tessa', 'Bahan Habis Pakai Medis', 'Bahan', 3, 'Box', 'Baik', '2026-11-09', 'Ruang Penyimpanan Keperawatan', '2026-11-09 05:10:52', '2026-11-09 13:10:52');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kerusakan_alat`
--

DROP TABLE IF EXISTS `tb_kerusakan_alat`;
CREATE TABLE IF NOT EXISTS `tb_kerusakan_alat` (
  `id_kerusakan` int(11) NOT NULL AUTO_INCREMENT,
  `id_peminjaman` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `id_laboran` int(11) NOT NULL,
  `kondisi_kerusakan` enum('Rusak Ringan','Rusak Berat') NOT NULL,
  `jumlah_rusak` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `deskripsi_kerusakan` text NOT NULL,
  `status_perbaikan` enum('Menunggu Perbaikan','Sedang Diperbaiki','Selesai Diperbaiki','Afkir/Diganti') NOT NULL DEFAULT 'Menunggu Perbaikan',
  `tgl_laporan` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_kerusakan`),
  KEY `id_peminjaman` (`id_peminjaman`),
  KEY `id_barang` (`id_barang`),
  KEY `fk_kerusakan_laboran` (`id_laboran`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kerusakan_alat`
--

INSERT INTO `tb_kerusakan_alat` (`id_kerusakan`, `id_peminjaman`, `id_barang`, `id_laboran`, `kondisi_kerusakan`, `jumlah_rusak`, `deskripsi_kerusakan`, `status_perbaikan`, `tgl_laporan`, `updated_at`) VALUES
(1, 27, 16, 2, 'Rusak Berat', 0, 'Barang pecah', 'Afkir/Diganti', '2026-10-26 07:51:00', '2026-11-07 22:29:13'),
(2, 28, 15, 2, 'Rusak Ringan', 0, 'Barang robek', 'Selesai Diperbaiki', '2026-10-26 07:51:20', '2026-11-07 22:28:57'),
(3, 29, 15, 1, 'Rusak Berat', 0, 'Alat dikembalikan dalam keadaan rusak.', 'Selesai Diperbaiki', '2026-10-27 01:20:59', '2026-11-07 22:29:24'),
(4, 34, 20, 2, 'Rusak Berat', 0, 'Alat dikembalikan dalam keadaan rusak.', 'Selesai Diperbaiki', '2026-11-07 14:21:59', '2026-11-07 22:29:27'),
(5, 37, 16, 2, 'Rusak Berat', 0, 'Alat dikembalikan dalam keadaan rusak.', 'Selesai Diperbaiki', '2026-11-08 11:57:12', '2026-11-08 19:57:55'),
(6, 38, 16, 2, 'Rusak Ringan', 0, 'Alat dikembalikan dalam keadaan rusak.', 'Selesai Diperbaiki', '2026-11-11 10:58:02', '2026-11-11 17:58:44'),
(7, 39, 17, 1, 'Rusak Berat', 1, 'Jahitan lepas', 'Selesai Diperbaiki', '2026-11-11 14:36:16', '2026-11-11 14:37:07');

-- --------------------------------------------------------

--
-- Table structure for table `tb_peminjaman`
--

DROP TABLE IF EXISTS `tb_peminjaman`;
CREATE TABLE IF NOT EXISTS `tb_peminjaman` (
  `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT,
  `kode_peminjaman` varchar(30) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah_pinjam` int(11) NOT NULL,
  `kondisi_sebelum` enum('Baik','Rusak Ringan','Rusak Berat') NOT NULL DEFAULT 'Baik',
  `kondisi_sesudah` enum('Baik','Rusak Ringan','Rusak Berat') DEFAULT NULL,
  `catatan_laboran` text DEFAULT NULL,
  `tgl_pinjam` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_kembali_rencana` datetime NOT NULL,
  `tgl_kembali_realisasi` datetime DEFAULT NULL,
  `status` enum('Pending','Disetujui','Ditolak','Dipinjam','Dikembalikan') NOT NULL DEFAULT 'Pending',
  `catatan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_peminjaman`),
  UNIQUE KEY `uk_kode_peminjaman` (`kode_peminjaman`),
  KEY `fk_pinjam_user` (`id_user`),
  KEY `fk_pinjam_barang` (`id_barang`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_peminjaman`
--

INSERT INTO `tb_peminjaman` (`id_peminjaman`, `kode_peminjaman`, `id_user`, `id_barang`, `jumlah_pinjam`, `kondisi_sebelum`, `kondisi_sesudah`, `catatan_laboran`, `tgl_pinjam`, `tgl_kembali_rencana`, `tgl_kembali_realisasi`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(14, 'PJM-20261022-001', 1, 15, 1, 'Baik', NULL, NULL, '2026-10-22 00:00:00', '2026-10-23 00:00:00', '2026-10-23 14:45:22', 'Dikembalikan', '', '2026-10-22 01:26:13', '2026-10-23 22:45:22'),
(17, 'PJM-20261024-001', 1, 16, 1, 'Baik', NULL, NULL, '2026-10-24 00:00:00', '2026-10-25 00:00:00', '2026-10-25 08:07:29', 'Dikembalikan', '', '2026-10-24 13:09:59', '2026-10-25 16:07:29'),
(18, 'PJM-20261024-002', 1, 25, 1, 'Baik', NULL, NULL, '2026-10-24 00:00:00', '2026-10-25 00:00:00', '2026-10-25 08:07:26', 'Dikembalikan', '', '2026-10-24 13:10:06', '2026-10-25 16:07:26'),
(19, 'PJM-20261024-003', 1, 16, 1, 'Baik', NULL, NULL, '2026-10-24 00:00:00', '2026-10-25 00:00:00', '2026-10-25 08:07:22', 'Dikembalikan', '', '2026-10-24 13:10:21', '2026-10-25 16:07:22'),
(20, 'PJM-20261025-001', 3, 27, 1, 'Baik', NULL, NULL, '2026-10-25 00:00:00', '2026-10-26 00:00:00', '2026-10-25 08:07:19', 'Dikembalikan', '', '2026-10-25 07:07:48', '2026-10-25 16:07:19'),
(21, 'PJM-20261025-002', 3, 25, 3, 'Baik', NULL, NULL, '2026-10-25 00:00:00', '2026-10-26 00:00:00', '2026-10-25 08:06:35', 'Dikembalikan', '', '2026-10-25 07:22:48', '2026-10-25 16:06:35'),
(23, 'PJM-20261025-850', 4, 15, 1, 'Baik', NULL, NULL, '2026-10-25 00:00:00', '2026-10-26 00:00:00', NULL, 'Ditolak', '-', '2026-10-25 08:13:40', '2026-10-26 15:05:01'),
(24, 'PJM-20261026-994', 2, 25, 1, 'Baik', NULL, NULL, '2026-10-26 00:00:00', '2026-10-27 00:00:00', '2026-10-26 07:06:38', 'Dikembalikan', '-', '2026-10-26 07:06:18', '2026-10-26 15:06:38'),
(25, 'PJM-20261026-470', 2, 25, 1, 'Baik', NULL, NULL, '2026-10-26 00:00:00', '2026-10-27 00:00:00', '2026-10-26 07:07:31', 'Dikembalikan', '-', '2026-10-26 07:06:48', '2026-10-26 15:07:31'),
(26, 'PJM-20261026-425', 3, 23, 1, 'Baik', NULL, NULL, '2026-10-26 00:00:00', '2026-10-27 00:00:00', '2026-10-26 07:09:56', 'Dikembalikan', '-', '2026-10-26 07:08:52', '2026-10-26 15:09:56'),
(27, 'PJM-20261026-401', 3, 16, 1, 'Baik', 'Rusak Berat', 'Barang pecah', '2026-10-26 00:00:00', '2026-10-27 00:00:00', NULL, 'Dikembalikan', '-', '2026-10-26 07:41:42', '2026-10-26 07:51:00'),
(28, 'PJM-20261026-679', 3, 15, 1, 'Baik', 'Rusak Ringan', 'Barang robek', '2026-10-26 00:00:00', '2026-10-27 00:00:00', NULL, 'Dikembalikan', '-', '2026-10-26 07:42:35', '2026-10-26 07:51:20'),
(29, 'PJM-20261026-229', 3, 15, 1, 'Baik', 'Rusak Berat', '', '2026-10-26 00:00:00', '2026-10-27 00:00:00', NULL, 'Dikembalikan', '-', '2026-10-26 07:49:38', '2026-10-27 01:20:59'),
(32, 'PJM-20261030-358', 1, 15, 1, 'Baik', 'Baik', '', '2026-10-30 00:00:00', '2026-10-31 00:00:00', NULL, 'Dikembalikan', '-', '2026-10-30 12:28:10', '2026-10-30 12:28:37'),
(34, 'PJM-20261105-141', 4, 20, 1, 'Baik', 'Rusak Berat', '', '2026-11-05 00:00:00', '2026-11-06 00:00:00', NULL, 'Dikembalikan', '-', '2026-11-05 07:34:34', '2026-11-07 14:21:59'),
(35, 'PJM-20261107-815', 3, 16, 1, 'Baik', 'Baik', '', '2026-11-07 00:00:00', '2026-11-08 00:00:00', NULL, 'Dikembalikan', '-', '2026-11-07 14:07:23', '2026-11-08 02:06:37'),
(37, 'PJM-20261108-991', 4, 16, 1, 'Baik', 'Rusak Berat', '', '2026-11-08 00:00:00', '2026-11-09 00:00:00', NULL, 'Dikembalikan', '-', '2026-11-08 11:55:28', '2026-11-08 11:57:12'),
(38, 'PJM-20261111-573', 4, 16, 1, 'Baik', 'Rusak Ringan', '', '2026-11-11 00:00:00', '2026-11-12 00:00:00', '2026-11-11 10:58:02', 'Dikembalikan', '-', '2026-11-11 10:55:53', '2026-11-11 10:58:02'),
(39, 'PJM-20261111-560', 3, 17, 1, 'Baik', 'Rusak Berat', 'Jahitan lepas', '2026-11-11 00:00:00', '2026-11-12 00:00:00', '2026-11-11 14:36:16', 'Dikembalikan', '-', '2026-11-11 14:35:47', '2026-11-11 14:36:16'),
(40, 'PJM-20261114-657', 1, 15, 1, 'Baik', 'Baik', '', '2026-11-14 00:00:00', '2026-11-15 00:00:00', '2026-11-14 06:53:51', 'Dikembalikan', 'buat test', '2026-11-14 06:51:46', '2026-11-14 06:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pengadaan`
--

DROP TABLE IF EXISTS `tb_pengadaan`;
CREATE TABLE IF NOT EXISTS `tb_pengadaan` (
  `id_pengadaan` int(11) NOT NULL AUTO_INCREMENT,
  `nama_item` varchar(150) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `satuan` varchar(30) NOT NULL,
  `estimasi_harga` decimal(12,2) NOT NULL,
  `alasan_pengadaan` text NOT NULL,
  `id_pengaju` int(11) NOT NULL,
  `status_approval` enum('Pending','Disetujui Laboran','Disetujui Kepsek','Ditolak','Selesai') NOT NULL DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pengadaan`),
  KEY `fk_pengadaan_user` (`id_pengaju`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pengadaan`
--

INSERT INTO `tb_pengadaan` (`id_pengadaan`, `nama_item`, `jumlah`, `satuan`, `estimasi_harga`, `alasan_pengadaan`, `id_pengaju`, `status_approval`, `created_at`) VALUES
(3, 'Masker Medis', 12, 'Box', 20000.00, '', 2, 'Selesai', '2026-10-25 07:06:23'),
(4, 'Masker Medis', 1, 'Pcs', 30000.00, 'Keperluan praktik USK', 2, 'Ditolak', '2026-11-07 14:48:56'),
(5, 'Alkohol swab', 10, 'Box', 25000.00, 'Keperluan persiapan USK kelas XII', 3, 'Selesai', '2026-11-07 15:28:14'),
(7, 'Alkohol Swab', 20, 'Box', 35000.00, '', 3, 'Selesai', '2026-11-08 11:59:39');

-- --------------------------------------------------------

--
-- Table structure for table `tb_penggunaan_bhp`
--

DROP TABLE IF EXISTS `tb_penggunaan_bhp`;
CREATE TABLE IF NOT EXISTS `tb_penggunaan_bhp` (
  `id_penggunaan` int(11) NOT NULL AUTO_INCREMENT,
  `kode_penggunaan` varchar(50) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah_pakai` int(11) NOT NULL DEFAULT 1,
  `tgl_penggunaan` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('Pending','Disetujui','Ditolak') NOT NULL DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_penggunaan`),
  KEY `id_user` (`id_user`),
  KEY `id_barang` (`id_barang`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_penggunaan_bhp`
--

INSERT INTO `tb_penggunaan_bhp` (`id_penggunaan`, `kode_penggunaan`, `id_user`, `id_barang`, `jumlah_pakai`, `tgl_penggunaan`, `keterangan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'BHP-20261026-510', 1, 27, 1, '2026-10-26', '', 'Disetujui', '2026-10-26 02:50:08', NULL),
(2, 'BHP-20261026-517', 3, 27, 1, '2026-10-26', '', 'Disetujui', '2026-10-26 02:55:12', '2026-10-26 02:55:44'),
(3, 'BHP-20261026-614', 3, 27, 2, '2026-10-26', '', 'Disetujui', '2026-10-26 02:57:53', '2026-10-26 03:02:25'),
(8, 'BHP-20261107-942', 4, 27, 1, '2026-11-07', '-', 'Disetujui', '2026-11-07 13:37:01', '2026-11-07 14:30:09');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

DROP TABLE IF EXISTS `tb_users`;
CREATE TABLE IF NOT EXISTS `tb_users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nip_nik_user` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role` enum('admin','laboran','guru','siswa','kepsek') NOT NULL DEFAULT 'siswa',
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id_user`, `nip_nik_user`, `username`, `password`, `nama_lengkap`, `email`, `foto`, `role`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, '2022.05.178', 'admin', '$2y$10$f/vQhgbf0hKSJU8dFPku8..iGWxTzi19HAFhxi5r/vsc2/Fh/X.OC', 'Nyoman Ayu Sinta Gayatari, A.Md.', 'smk_suryamedika@yahoo.com', 'user_1794110474.png', 'admin', 1, '2026-09-19 14:48:30', '2026-11-08 04:01:14'),
(2, '2026.21.313', 'laboran_desiluis', '$2y$10$95a7EeGzi2Je.UsVwycyGOtw0Q96IxkAipTIMpeWU9DJAHiK.2yoq', 'Desy Natalia Luis, S.Tr.Keb.', 'desyluis@gmail.com', 'user_2_1790304104.png', 'laboran', 1, '2026-09-19 14:48:30', '2026-11-08 05:46:52'),
(3, '2017.03.008', 'guru_yuli', '$2y$10$ZyQKoWZ46XlNi6jIlSvTE.6bNuHgnm9wtcnRv8GWHkbYCz4z8/MRi', 'Ns.Luh Putu Yuli Priskayanti,S.Kep.', 'yulypriska1988@gmail.com', NULL, 'guru', 1, '2026-09-19 14:48:30', '2026-11-08 05:47:19'),
(4, '0099858971', 'siswa_adel', '$2y$10$n91lo5x.n12PkfkRUC4XfOdyeOoIIdQ7K8eQymhHFCWqGouXbFPZK', 'Adelia Mifta Sari', 'adelia07@gmail.com', NULL, 'siswa', 1, '2026-09-19 14:48:30', '2026-11-08 03:23:28'),
(5, '2016.06.120', 'kepsek_ayudesi', '$2y$10$qnChHgVNt83.K16VQy9fIOMmYdAtHIJvBtaBiOj2Pm4k4KRyzrese', 'Nyoman Ayu Sri Desi Ariani, S.Pd.,M.Pd.', 'ayusridesia@yahoo.com', NULL, 'kepsek', 1, '2026-09-19 14:48:30', '2026-11-08 03:20:10');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_kerusakan_alat`
--
ALTER TABLE `tb_kerusakan_alat`
  ADD CONSTRAINT `fk_kerusakan_barang` FOREIGN KEY (`id_barang`) REFERENCES `tb_inventaris` (`id_barang`),
  ADD CONSTRAINT `fk_kerusakan_laboran` FOREIGN KEY (`id_laboran`) REFERENCES `tb_users` (`id_user`),
  ADD CONSTRAINT `fk_kerusakan_peminjaman` FOREIGN KEY (`id_peminjaman`) REFERENCES `tb_peminjaman` (`id_peminjaman`);

--
-- Constraints for table `tb_peminjaman`
--
ALTER TABLE `tb_peminjaman`
  ADD CONSTRAINT `fk_pinjam_barang` FOREIGN KEY (`id_barang`) REFERENCES `tb_inventaris` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pinjam_user` FOREIGN KEY (`id_user`) REFERENCES `tb_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_pengadaan`
--
ALTER TABLE `tb_pengadaan`
  ADD CONSTRAINT `fk_pengadaan_user` FOREIGN KEY (`id_pengaju`) REFERENCES `tb_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_penggunaan_bhp`
--
ALTER TABLE `tb_penggunaan_bhp`
  ADD CONSTRAINT `fk_bhp_barang` FOREIGN KEY (`id_barang`) REFERENCES `tb_inventaris` (`id_barang`),
  ADD CONSTRAINT `fk_bhp_user` FOREIGN KEY (`id_user`) REFERENCES `tb_users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
