-- phpMyAdmin SQL Dump
-- version 5.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- TABEL ANGGOTA
-- =====================================================

CREATE TABLE `tb_anggota` (
  `id_anggota` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(111) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `foto` varchar(245) NOT NULL DEFAULT 'default.png',
  PRIMARY KEY (`id_anggota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL KATEGORI
-- =====================================================

CREATE TABLE `tb_kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL SUPPLIER
-- =====================================================

CREATE TABLE `tb_supplier` (
  `id_supplier` int(11) NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL BARANG
-- =====================================================

CREATE TABLE `tb_barang` (
  `id_barang` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `nama_gambar` varchar(255) NOT NULL,
  `tgl_kadaluarsa` date DEFAULT NULL,

  -- TAMBAHAN BARU
  `id_kategori` int(11) DEFAULT NULL,
  `id_supplier` int(11) DEFAULT NULL,

  PRIMARY KEY (`id_barang`),

  KEY `id_kategori` (`id_kategori`),
  KEY `id_supplier` (`id_supplier`),

  CONSTRAINT `fk_barang_kategori`
  FOREIGN KEY (`id_kategori`)
  REFERENCES `tb_kategori` (`id_kategori`)
  ON DELETE SET NULL
  ON UPDATE CASCADE,

  CONSTRAINT `fk_barang_supplier`
  FOREIGN KEY (`id_supplier`)
  REFERENCES `tb_supplier` (`id_supplier`)
  ON DELETE SET NULL
  ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL BARANG MASUK
-- =====================================================

CREATE TABLE `tb_barang_masuk` (
  `id_masuk` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal` date NOT NULL,

  PRIMARY KEY (`id_masuk`),
  KEY `id_barang` (`id_barang`),

  CONSTRAINT `tb_barang_masuk_ibfk_1`
  FOREIGN KEY (`id_barang`)
  REFERENCES `tb_barang` (`id_barang`)
  ON DELETE CASCADE
  ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL BARANG KELUAR
-- =====================================================

CREATE TABLE `tb_barang_keluar` (
  `id_keluar` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `tujuan` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,

  PRIMARY KEY (`id_keluar`),
  KEY `id_barang` (`id_barang`),

  CONSTRAINT `fk_barang_keluar`
  FOREIGN KEY (`id_barang`)
  REFERENCES `tb_barang` (`id_barang`)
  ON DELETE CASCADE
  ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL PINJAMAN
-- =====================================================

CREATE TABLE `tb_pinjaman` (
  `id_pinjaman` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('dipinjam','kembali','Menunggu','Disetujui','Ditolak') DEFAULT 'dipinjam',
  `id_anggota` int(255) NOT NULL,
  `jangka_waktu` int(11) NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `jaminan` varchar(100) NOT NULL,
  `tanggal_kembali` int(11) DEFAULT NULL,

  PRIMARY KEY (`id_pinjaman`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL SIMPANAN
-- =====================================================

CREATE TABLE `tb_simpanan` (
  `id_simpanan` int(11) NOT NULL AUTO_INCREMENT,
  `id_anggota` int(11) NOT NULL,
  `jenis_simpanan` varchar(255) NOT NULL,
  `nominal` int(11) NOT NULL,
  `tanggal` date NOT NULL,

  PRIMARY KEY (`id_simpanan`),
  KEY `id_anggota` (`id_anggota`),

  CONSTRAINT `tb_simpanan_ibfk_1`
  FOREIGN KEY (`id_anggota`)
  REFERENCES `tb_anggota` (`id_anggota`)
  ON DELETE CASCADE
  ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL USER
-- =====================================================

CREATE TABLE `tb_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager') NOT NULL,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- DATA USER
-- =====================================================

INSERT INTO `tb_user`
(`id`, `username`, `password`, `role`)
VALUES
(1, 'admin', '12345', 'admin'),
(2, 'manager', '12345', 'manager');

COMMIT;