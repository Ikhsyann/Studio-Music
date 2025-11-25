-- Active: 1763951621490@@127.0.0.1@3306@studio_music
-- ================================================
-- Database: studio_music
-- Created: 24 November 2025
-- Description: Database untuk Sistem Booking Studio Musik
-- ================================================

-- Drop database jika sudah ada dan buat ulang
DROP DATABASE IF EXISTS studio_music;
CREATE DATABASE studio_music CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE studio_music;

-- ================================================
-- Table: users
-- Description: Menyimpan data pengguna/pelanggan
-- ================================================
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    no_telp VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- Table: admin
-- Description: Menyimpan data administrator
-- ================================================
CREATE TABLE admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- Table: studios
-- Description: Menyimpan data studio musik
-- ================================================
CREATE TABLE studios (
    id_studio INT AUTO_INCREMENT PRIMARY KEY,
    nama_studio VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga_per_jam DECIMAL(10,2) NOT NULL,
    fasilitas TEXT,
    kapasitas INT NOT NULL,
    gambar VARCHAR(255),
    status_ketersediaan ENUM('Tersedia', 'Tidak Tersedia') DEFAULT 'Tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status_ketersediaan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- Table: booking
-- Description: Menyimpan data booking studio
-- ================================================
CREATE TABLE booking (
    id_booking INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_studio INT NOT NULL,
    tanggal_main DATE NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    total_bayar DECIMAL(10,2) NOT NULL,
    status_booking ENUM('Menunggu Konfirmasi', 'Disetujui', 'Selesai', 'Dibatalkan') DEFAULT 'Menunggu Konfirmasi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_studio) REFERENCES studios(id_studio) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_user (id_user),
    INDEX idx_studio (id_studio),
    INDEX idx_tanggal (tanggal_main),
    INDEX idx_status (status_booking)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- Table: payments
-- Description: Menyimpan data pembayaran booking
-- ================================================
CREATE TABLE payments (
    id_payment INT AUTO_INCREMENT PRIMARY KEY,
    id_booking INT NOT NULL,
    jumlah_bayar DECIMAL(10,2) NOT NULL,
    metode_pembayaran ENUM('Transfer Bank', 'E-Wallet', 'Cash') NOT NULL,
    bukti_pembayaran VARCHAR(255),
    tanggal_pembayaran TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    keterangan TEXT,
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_booking (id_booking)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- INSERT SAMPLE DATA
-- ================================================

-- Data Admin (password: admin123)
INSERT INTO admin (email, password) VALUES
('admin@studiomusik.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Data Users (password: user123)
INSERT INTO users (nama, email, password, no_telp) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891'),
('Bob Wilson', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567892');

-- Data Studios
INSERT INTO studios (nama_studio, deskripsi, harga_per_jam, fasilitas, kapasitas, gambar, status_ketersediaan) VALUES
('Studio A - Premium', 'Studio recording dengan peralatan profesional, akustik terbaik', 150000.00, 'Microphone Condenser, Audio Interface, Drum Set, Keyboard, Guitar Amp, Soundproof Room', 10, 'studio-a.jpg', 'Tersedia'),
('Studio B - Standard', 'Studio untuk latihan band dengan peralatan lengkap', 100000.00, 'Drum Set, Bass Amp, Guitar Amp, Microphone, Mixer', 8, 'studio-b.jpg', 'Tersedia'),
('Studio C - Recording', 'Studio khusus recording dengan engineer berpengalaman', 200000.00, 'Professional Microphone, DAW Software, Audio Interface, Mixing Console, Monitor Speaker', 5, 'studio-c.jpg', 'Tersedia'),
('Studio D - Practice', 'Studio ekonomis untuk latihan personal atau duo', 75000.00, 'Guitar Amp, Bass Amp, Microphone, Speaker', 4, 'studio-d.jpg', 'Tersedia');

-- Data Booking Sample
INSERT INTO booking (id_user, id_studio, tanggal_main, jam_mulai, jam_selesai, total_bayar, status_booking) VALUES
(1, 1, '2025-11-25', '10:00:00', '12:00:00', 300000.00, 'Disetujui'),
(2, 2, '2025-11-25', '14:00:00', '16:00:00', 200000.00, 'Menunggu Konfirmasi'),
(3, 3, '2025-11-26', '09:00:00', '11:00:00', 400000.00, 'Disetujui');

-- Data Payments Sample
INSERT INTO payments (id_booking, jumlah_bayar, metode_pembayaran, bukti_pembayaran, keterangan) VALUES
(1, 300000.00, 'Transfer Bank', 'bukti_transfer_001.jpg', 'Pembayaran untuk booking Studio A'),
(3, 400000.00, 'E-Wallet', 'bukti_ewallet_002.jpg', 'Pembayaran untuk booking Studio C');

-- ================================================
-- END OF SQL SCRIPT
-- ================================================
