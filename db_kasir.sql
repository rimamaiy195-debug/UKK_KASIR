-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2026 at 08:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `aktivitas`
--

CREATE TABLE `aktivitas` (
  `aktivitas_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `waktu` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aktivitas`
--

INSERT INTO `aktivitas` (`aktivitas_id`, `user_id`, `aktivitas`, `waktu`) VALUES
(1, 1, 'Melkukan transaksi penjualan #', '2026-10-02 11:05:13'),
(2, 1, 'Menghapus produk ', '2026-10-04 11:17:07'),
(3, 1, 'Menambahkan produk ', '2026-10-04 11:17:19'),
(4, 1, 'Menambahkan pelanggan ', '2026-10-04 11:17:29'),
(5, 1, 'Menambahkan produk Marveluna by Itakrn', '2026-10-04 11:19:48'),
(6, 1, 'Login ke sistem', '2026-10-08 13:50:06'),
(7, 1, 'Login ke sistem', '2026-10-15 14:53:51'),
(8, 2, 'Login ke sistem', '2026-10-15 14:54:28'),
(9, 1, 'Login ke sistem', '2026-10-15 20:03:47'),
(10, 1, 'Mengubah transaksi penjualan #9', '2026-10-15 20:08:39');

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `detail_id` int(11) NOT NULL,
  `penjualan_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `jumlah_produk` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`detail_id`, `penjualan_id`, `produk_id`, `jumlah_produk`, `subtotal`) VALUES
(1, 1, 1, 1, 200000.00),
(2, 2, 3, 2, 20000.00),
(3, 3, 3, 3, 30000.00),
(4, 3, 1, 2, 400000.00),
(5, 4, 3, 2, 20000.00),
(6, 4, 1, 1, 200000.00),
(7, 5, 4, 2, 3800000.00),
(8, 6, 5, 2, 198000.00),
(9, 7, 6, 1, 99000.00),
(10, 7, 7, 1, 99000.00),
(11, 8, 6, 1, 99000.00),
(12, 8, 7, 1, 99000.00),
(13, 8, 5, 1, 99000.00),
(14, 8, 8, 3, 4500.00),
(16, 9, 9, 1, 99000.00);

--
-- Triggers `detail_penjualan`
--
DELIMITER $$
CREATE TRIGGER `kembalikan_stok` AFTER DELETE ON `detail_penjualan` FOR EACH ROW BEGIN
	UPDATE produk SET stok = stok + OLD.jumlah_produk 
    WHERE produk_id = OLD.produk_id;
    END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `kurangi_stok` AFTER INSERT ON `detail_penjualan` FOR EACH ROW BEGIN
	UPDATE produk SET stok = stok - NEW.jumlah_produk
    WHERE produk_id = NEW.produk_id;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `pelanggan_id` int(11) NOT NULL,
  `nama_pelanggan` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `nomor_telepon` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`pelanggan_id`, `nama_pelanggan`, `alamat`, `nomor_telepon`) VALUES
(1, 'Naren', 'Semarang', '08123456789'),
(2, 'Mahen', 'Semarang', '086767676767'),
(3, 'Seano', 'Semarang', '0897654321'),
(4, 'Reina', 'Semarang', '0834567890');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `penjualan_id` int(11) NOT NULL,
  `tanggal_penjualan` datetime NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `diskon` decimal(5,2) NOT NULL DEFAULT 0.00,
  `pelanggan_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`penjualan_id`, `tanggal_penjualan`, `total_harga`, `diskon`, `pelanggan_id`) VALUES
(6, '2026-09-23 10:25:56', 198000.00, 0.00, 2),
(7, '2026-09-23 10:33:48', 198000.00, 0.00, 1),
(8, '2026-09-23 10:57:59', 301500.00, 0.00, 3),
(9, '2026-09-30 10:42:45', 89100.00, 10.00, 4);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `produk_id` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`produk_id`, `nama_produk`, `harga`, `stok`, `gambar`) VALUES
(5, 'Novel Samuel by Itakrn', 99000.00, 47, '1790133905_samuel by itakrn.jpg'),
(6, 'Novel Laut Pasang 1994 by Lilpudu', 99000.00, 48, '1790134071_laut-pasang.jpg'),
(7, 'Novel Laut Pasang 1994 2 by Lilpudu', 99000.00, 48, '1790134320_laut-pasang-2.jfif'),
(8, 'Bookmarks - Dream Big Bookmarks/pcs', 1500.00, 97, '1790135632_03-15822_popup.jpg'),
(9, 'Novel Eccendentesiast by Itakrn', 99000.00, 49, '1790234311_eccendentesiast.jfif'),
(12, 'Marveluna by Itakrn', 99000.00, 50, '1791087588_marveluna by itakrn.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('admin','kasir','owner') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `level`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'admin'),
(2, 'kasir', 'de28f8f7998f23ab4194b51a6029416f', 'kasir'),
(3, 'owner', '5be057accb25758101fa5eadbbd79503', 'owner');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD PRIMARY KEY (`aktivitas_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`detail_id`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`pelanggan_id`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`penjualan_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`produk_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aktivitas`
--
ALTER TABLE `aktivitas`
  MODIFY `aktivitas_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `pelanggan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `penjualan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `produk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD CONSTRAINT `aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
