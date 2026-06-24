-- Struktur tabel tb_insentif

CREATE TABLE `tb_insentif` (
  `id_insentif` int(11) NOT NULL AUTO_INCREMENT,
  `layanan` varchar(255) NOT NULL,
  `salesman` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `pendapatan` varchar(255) NOT NULL,
  `persentase` varchar(255) NOT NULL,
  `insentif_penjualan` varchar(255) NOT NULL,
  `insentif` varchar(255) NOT NULL,
  PRIMARY KEY (`id_insentif`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Struktur tabel tb_marketing

CREATE TABLE `tb_marketing` (
  `id_marketing` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `nama_klien` varchar(255) NOT NULL,
  `tanggal_kunjungan` date NOT NULL,
  `bukti` varchar(255) NOT NULL,
  `layanan_kes` varchar(255) NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `nilai` varchar(255) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Penawaran & Kunjungan',
  PRIMARY KEY (`id_marketing`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Struktur tabel tb_riwayat

CREATE TABLE `tb_riwayat` (
  `id_riwayat` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `pekerjaan` varchar(255) NOT NULL,
  PRIMARY KEY (`id_riwayat`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data tabel tb_riwayat

INSERT INTO tb_riwayat VALUES('8', '12', 'Menambahkan aktivitas marketing');
INSERT INTO tb_riwayat VALUES('9', '12', 'update riwayat aktivitas klien');
INSERT INTO tb_riwayat VALUES('10', '12', 'update riwayat aktivitas klien');
INSERT INTO tb_riwayat VALUES('11', '12', 'update riwayat aktivitas klien');

-- Struktur tabel tb_user

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verify_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expire` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data tabel tb_user

INSERT INTO tb_user VALUES('11', 'manajermonitoring@gmail.com', 'Manajer Monitoring', '$2y$10$dZ1Iyre04DLhERrq8hl/guuyag0IrAsdVGNgCjXBXKT/lUkA8qtly', 'manajer', 'logout', '1', NULL, NULL, NULL);
INSERT INTO tb_user VALUES('12', 'amonitoring606@gmail.com', 'Admin monitoring', '$2y$10$Ybp6SEO43KE8bSyW40YIx.ZdgX6r.hpOFLY.6W3dH22z5eliwG3o6', 'admin', 'login', '1', NULL, NULL, NULL);

