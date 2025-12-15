-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 08:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `studio_music`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `email`, `password`, `created_at`) VALUES
(1, 'admin@studiomusik.com', '$2y$12$161UTbI2QaJ4nAdm4lp5WekiZz2Mw1xhk9G62Xy0tFRabhCbSLuYe', '2025-11-25 20:42:53'),
(2, 'muhammadikhsyan9@gmail.com', '$2y$12$w5sURh/EPQC.XM1Rnsj2FeV0IIoXoX6gG9I45T4JvBcqlCyLFjoYG', '2025-12-06 01:19:53');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id_booking` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_studio` int(11) NOT NULL,
  `tanggal_main` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `total_bayar` decimal(10,2) NOT NULL,
  `status_booking` enum('Menunggu Konfirmasi','Disetujui','Selesai','Dibatalkan','Ditolak') DEFAULT 'Menunggu Konfirmasi',
  `id_admin` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id_booking`, `id_user`, `id_studio`, `tanggal_main`, `jam_mulai`, `jam_selesai`, `total_bayar`, `status_booking`, `id_admin`, `created_at`, `deleted_at`) VALUES
(1, 1, 5, '2025-11-27', '10:00:00', '13:00:00', 3000000.00, 'Disetujui', 2, '2025-11-25 20:46:35', NULL),
(2, 1, 1, '2025-11-27', '10:00:00', '13:00:00', 450000.00, 'Dibatalkan', 2, '2025-11-25 20:47:07', NULL),
(3, 1, 1, '2025-11-26', '19:00:00', '22:00:00', 450000.00, 'Dibatalkan', 2, '2025-11-25 20:47:45', NULL),
(4, 1, 1, '2025-11-26', '14:00:00', '17:00:00', 450000.00, 'Disetujui', 2, '2025-11-25 20:48:31', NULL),
(5, 1, 5, '2025-12-06', '15:00:00', '22:00:00', 7000000.00, 'Disetujui', 2, '2025-11-26 16:56:31', NULL),
(6, 1, 1, '2025-12-03', '10:00:00', '13:00:00', 450000.00, 'Disetujui', 2, '2025-12-02 20:39:15', NULL),
(7, 1, 5, '2025-12-07', '10:00:00', '13:00:00', 3000000.00, 'Disetujui', 1, '2025-12-07 05:46:08', NULL),
(8, 1, 5, '2025-12-18', '10:00:00', '13:00:00', 3000000.00, 'Dibatalkan', NULL, '2025-12-08 05:43:24', NULL),
(9, 1, 5, '2025-12-10', '11:00:00', '14:00:00', 3000000.00, 'Dibatalkan', NULL, '2025-12-10 09:20:24', NULL),
(10, 3, 5, '2025-12-10', '10:00:00', '12:00:00', 2000000.00, 'Dibatalkan', 1, '2025-12-10 09:27:15', NULL),
(11, 3, 5, '2025-12-10', '12:00:00', '14:00:00', 2000000.00, 'Disetujui', 1, '2025-12-10 09:28:41', NULL),
(12, 3, 6, '2025-12-18', '12:00:00', '14:00:00', 200000.00, 'Dibatalkan', NULL, '2025-12-11 06:34:29', NULL),
(13, 3, 5, '2025-12-11', '10:00:00', '12:00:00', 2000000.00, 'Dibatalkan', NULL, '2025-12-11 06:38:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id_payment` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `metode_pembayaran` enum('Transfer Bank','E-Wallet') NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_pembayaran` timestamp NULL DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id_payment`, `id_booking`, `jumlah_bayar`, `metode_pembayaran`, `bukti_pembayaran`, `tanggal_pembayaran`, `keterangan`) VALUES
(1, 1, 3000000.00, 'Transfer Bank', 'payment_1_1764132395.jpg', '2025-11-25 20:46:35', ''),
(2, 2, 450000.00, 'Transfer Bank', 'payment_2_1764132427.jpg', '2025-11-25 20:47:07', ''),
(3, 3, 450000.00, 'Transfer Bank', 'payment_3_1764132465.jpeg', '2025-11-25 20:47:45', ''),
(4, 4, 450000.00, 'Transfer Bank', 'payment_4_1764132511.jpg', '2025-11-25 20:48:31', ''),
(5, 5, 7000000.00, 'E-Wallet', 'payment_5_1764204991.jpg', '2025-11-26 16:56:31', 'knnjnjn'),
(6, 6, 450000.00, 'Transfer Bank', 'payment_6_1764736755.jpg', '2025-12-02 20:39:15', ''),
(7, 7, 3000000.00, 'E-Wallet', 'payment_7_1765115168.png', '2025-12-07 05:46:08', ''),
(8, 8, 3000000.00, 'Transfer Bank', 'payment_8_1765172604.png', '2025-12-08 05:43:24', 'ass'),
(9, 9, 3000000.00, 'E-Wallet', 'payment_9_1765358424.png', '2025-12-10 09:20:24', 'a'),
(10, 10, 2000000.00, 'Transfer Bank', 'payment_10_1765358835.png', '2025-12-10 09:27:15', ''),
(11, 11, 2000000.00, 'E-Wallet', 'payment_11_1765358921.png', '2025-12-10 09:28:41', ''),
(12, 12, 200000.00, 'Transfer Bank', 'payment_12_1765434869.jpg', '2025-12-11 06:34:29', ''),
(13, 13, 2000000.00, 'Transfer Bank', 'payment_13_1765435109.jpg', '2025-12-11 06:38:29', '');

-- --------------------------------------------------------

--
-- Table structure for table `studios`
--

CREATE TABLE `studios` (
  `id_studio` int(11) NOT NULL,
  `nama_studio` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL,
  `fasilitas` text DEFAULT NULL,
  `kapasitas` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status_ketersediaan` enum('Tersedia','Tidak Tersedia') DEFAULT 'Tersedia',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `studios`
--

INSERT INTO `studios` (`id_studio`, `nama_studio`, `deskripsi`, `harga_per_jam`, `fasilitas`, `kapasitas`, `gambar`, `status_ketersediaan`, `created_at`) VALUES
(1, 'Studio A - Premium', 'Studio recording dengan peralatan profesional, akustik terbaik', 150000.00, 'Microphone Condenser, Audio Interface, Drum Set, Keyboard, Guitar Amp, Soundproof Room', 10, 'Studio_A.jpeg', 'Tersedia', '2025-11-25 20:42:54'),
(2, 'Studio B - Standard', 'Studio untuk latihan band dengan peralatan lengkap', 100000.00, 'Drum Set, Bass Amp, Guitar Amp, Microphone, Mixer', 8, 'Studio_B.jpeg', 'Tersedia', '2025-11-25 20:42:54'),
(3, 'Studio C - Recording', 'Studio khusus recording dengan engineer berpengalaman', 200000.00, 'Professional Microphone, DAW Software, Audio Interface, Mixing Console, Monitor Speaker', 5, 'Studio_C.jpeg', 'Tersedia', '2025-11-25 20:42:54'),
(4, 'Studio D - Ekonomis', 'Studio ekonomis untuk latihan personal atau duo', 75000.00, 'Guitar Amp, Bass Amp, Microphone, Speaker', 4, 'Studio_D.jpeg', 'Tersedia', '2025-11-25 20:42:54'),
(5, 'Anti-Normal Studio', 'Produce Music', 1000000.00, 'Microphone, Drum, Encoder, Hipdut', 10, 'Studio_F.jpg', 'Tersedia', '2025-11-25 20:45:40'),
(6, 'Kelompok 4-Studio', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.', 100000.00, 'Microphone, Tumbler Tuku', 4, 'kelompok-4-studio.jpg', 'Tersedia', '2025-12-07 06:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telp` varchar(15) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `no_telp`, `created_at`, `deleted_at`) VALUES
(1, 'Muhammad Ikhsyan', '10241049@student.itk.ac.id', '$2y$12$Fv0Pzc8CzuIbYYmN88zT0.OVvdwkVyLAM6XlDIZVk3UiXSAw//8hO', '085705012504', '2025-11-25 20:42:57', NULL),
(2, 'coba user', 'cobauser@gmail.com', '$2y$12$ZA.kdjZzuIoPWeRHkBpAaOeBdyfKpf7fy8sJEs6oJX2hlCpPcBduO', '123456788910', '2025-12-06 01:19:16', NULL),
(3, 'Dawwas Eryansyah Pratama', '10241019@student.itk.ac.id', '$2y$12$Gwtvgk7BanmPhy.HF/.lHOZREp4O.EQk4BdMuq4mmVuCuTRihfAa.', '12345678911', '2025-12-07 20:58:18', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_unique` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `idx_user` (`id_user`),
  ADD KEY `idx_studio` (`id_studio`),
  ADD KEY `idx_tanggal` (`tanggal_main`),
  ADD KEY `idx_status` (`status_booking`),
  ADD KEY `idx_admin` (`id_admin`),
  ADD KEY `idx_id_user` (`id_user`),
  ADD KEY `idx_id_studio` (`id_studio`),
  ADD KEY `idx_tanggal_main` (`tanggal_main`),
  ADD KEY `idx_status_booking` (`status_booking`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id_payment`),
  ADD KEY `idx_booking` (`id_booking`),
  ADD KEY `idx_id_booking` (`id_booking`),
  ADD KEY `idx_tanggal_pembayaran` (`tanggal_pembayaran`);

--
-- Indexes for table `studios`
--
ALTER TABLE `studios`
  ADD PRIMARY KEY (`id_studio`),
  ADD KEY `idx_status` (`status_ketersediaan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_unique` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id_payment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `studios`
--
ALTER TABLE `studios`
  MODIFY `id_studio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_studio`) REFERENCES `studios` (`id_studio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_studio` FOREIGN KEY (`id_studio`) REFERENCES `studios` (`id_studio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_booking` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
