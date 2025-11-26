-- phpMyAdmin SQL Dump
-- version 5.2.3-1.fc43
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 26, 2025 at 04:50 AM
-- Server version: 10.11.13-MariaDB
-- PHP Version: 8.4.14

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
(1, 'admin@studiomusik.com', '$2y$12$161UTbI2QaJ4nAdm4lp5WekiZz2Mw1xhk9G62Xy0tFRabhCbSLuYe', '2025-11-26 04:42:53');

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
  `status_booking` enum('Menunggu Konfirmasi','Disetujui','Selesai','Dibatalkan') DEFAULT 'Menunggu Konfirmasi',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id_booking`, `id_user`, `id_studio`, `tanggal_main`, `jam_mulai`, `jam_selesai`, `total_bayar`, `status_booking`, `created_at`) VALUES
(1, 1, 5, '2025-11-27', '10:00:00', '13:00:00', 3000000.00, 'Disetujui', '2025-11-26 04:46:35'),
(2, 1, 1, '2025-11-27', '10:00:00', '13:00:00', 450000.00, 'Dibatalkan', '2025-11-26 04:47:07'),
(3, 1, 1, '2025-11-26', '19:00:00', '22:00:00', 450000.00, 'Dibatalkan', '2025-11-26 04:47:45'),
(4, 1, 1, '2025-11-26', '14:00:00', '17:00:00', 450000.00, 'Menunggu Konfirmasi', '2025-11-26 04:48:31');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id_payment` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `metode_pembayaran` enum('Transfer Bank','E-Wallet','Cash') NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_pembayaran` timestamp NULL DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id_payment`, `id_booking`, `jumlah_bayar`, `metode_pembayaran`, `bukti_pembayaran`, `tanggal_pembayaran`, `keterangan`) VALUES
(1, 1, 3000000.00, 'Transfer Bank', 'payment_1_1764132395.jpg', '2025-11-26 04:46:35', ''),
(2, 2, 450000.00, 'Transfer Bank', 'payment_2_1764132427.jpg', '2025-11-26 04:47:07', ''),
(3, 3, 450000.00, 'Transfer Bank', 'payment_3_1764132465.jpeg', '2025-11-26 04:47:45', ''),
(4, 4, 450000.00, 'Transfer Bank', 'payment_4_1764132511.jpg', '2025-11-26 04:48:31', '');

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
(1, 'Studio A - Premium', 'Studio recording dengan peralatan profesional, akustik terbaik', 150000.00, 'Microphone Condenser, Audio Interface, Drum Set, Keyboard, Guitar Amp, Soundproof Room', 10, 'Studio_A.jpeg', 'Tersedia', '2025-11-26 04:42:54'),
(2, 'Studio B - Standard', 'Studio untuk latihan band dengan peralatan lengkap', 100000.00, 'Drum Set, Bass Amp, Guitar Amp, Microphone, Mixer', 8, 'Studio_B.jpeg', 'Tersedia', '2025-11-26 04:42:54'),
(3, 'Studio C - Recording', 'Studio khusus recording dengan engineer berpengalaman', 200000.00, 'Professional Microphone, DAW Software, Audio Interface, Mixing Console, Monitor Speaker', 5, 'Studio_C.jpeg', 'Tersedia', '2025-11-26 04:42:54'),
(4, 'Studio D - Ekonomis', 'Studio ekonomis untuk latihan personal atau duo', 75000.00, 'Guitar Amp, Bass Amp, Microphone, Speaker', 4, 'Studio_D.jpeg', 'Tersedia', '2025-11-26 04:42:54'),
(5, 'Anti-Normal Studio', 'Produce Music', 1000000.00, 'Microphone, Drum, Encoder, Hipdut', 10, 'Studio_F.jpg', 'Tersedia', '2025-11-26 04:45:40');

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
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `no_telp`, `created_at`) VALUES
(1, 'Muhammad Ikhsyan', '10241049@student.itk.ac.id', '$2y$12$Fv0Pzc8CzuIbYYmN88zT0.OVvdwkVyLAM6XlDIZVk3UiXSAw//8hO', '085705012504', '2025-11-26 04:42:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `idx_user` (`id_user`),
  ADD KEY `idx_studio` (`id_studio`),
  ADD KEY `idx_tanggal` (`tanggal_main`),
  ADD KEY `idx_status` (`status_booking`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id_payment`),
  ADD KEY `idx_booking` (`id_booking`);

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
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id_payment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `studios`
--
ALTER TABLE `studios`
  MODIFY `id_studio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_studio`) REFERENCES `studios` (`id_studio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
