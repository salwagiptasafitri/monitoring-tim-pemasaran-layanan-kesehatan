-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2026 at 02:14 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `monitoring_pemasaran`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_insentif`
--

CREATE TABLE `tb_insentif` (
  `id_insentif` int(11) NOT NULL,
  `layanan` varchar(255) NOT NULL,
  `salesman` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `pendapatan` varchar(255) NOT NULL,
  `persentase` varchar(255) NOT NULL,
  `insentif_penjualan` varchar(255) NOT NULL,
  `insentif` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_marketing`
--

CREATE TABLE `tb_marketing` (
  `id_marketing` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_klien` varchar(255) NOT NULL,
  `tanggal_kunjungan` date NOT NULL,
  `bukti` varchar(255) NOT NULL,
  `layanan_kes` varchar(255) NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `nilai` varchar(255) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Penawaran & Kunjungan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_riwayat`
--

CREATE TABLE `tb_riwayat` (
  `id_riwayat` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `pekerjaan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verify_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `email`, `nama`, `password`, `role`, `status`, `email_verified`, `verify_token`, `reset_token`, `reset_expire`) VALUES
(11, 'manajermonitoring@gmail.com', 'Manajer Monitoring', '$2y$10$dZ1Iyre04DLhERrq8hl/guuyag0IrAsdVGNgCjXBXKT/lUkA8qtly', 'tim_marketing', 'logout', 1, NULL, NULL, NULL),
(12, 'amonitoring606@gmail.com', 'Admin monitoring', '$2y$10$Ybp6SEO43KE8bSyW40YIx.ZdgX6r.hpOFLY.6W3dH22z5eliwG3o6', 'tim_marketing', 'logout', 1, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_insentif`
--
ALTER TABLE `tb_insentif`
  ADD PRIMARY KEY (`id_insentif`);

--
-- Indexes for table `tb_marketing`
--
ALTER TABLE `tb_marketing`
  ADD PRIMARY KEY (`id_marketing`);

--
-- Indexes for table `tb_riwayat`
--
ALTER TABLE `tb_riwayat`
  ADD PRIMARY KEY (`id_riwayat`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_insentif`
--
ALTER TABLE `tb_insentif`
  MODIFY `id_insentif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_marketing`
--
ALTER TABLE `tb_marketing`
  MODIFY `id_marketing` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tb_riwayat`
--
ALTER TABLE `tb_riwayat`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
