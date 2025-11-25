<?php

require_once __DIR__ . '/../../core/Controller.php';

class AdminController extends Controller {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function dashboard() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
        
        $adminModel = $this->model('Admin');
        $bookingModel = $this->model('Booking');
        $studioModel = $this->model('Studio');
        $userModel = $this->model('User');
        
        $totalBookings = count($bookingModel->all());
        $totalStudios = count($studioModel->all());
        $totalUsers = count($userModel->all());
        $allBookings = $adminModel->getAllBookings();
        
        $data = [
            'admin' => $_SESSION['admin'],
            'totalBookings' => $totalBookings,
            'totalStudios' => $totalStudios,
            'totalUsers' => $totalUsers,
            'bookings' => $allBookings,
            'title' => 'Admin Dashboard'
        ];
        
        $this->view('admin/dashboard', $data);
    }

    public function verifyPayment() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }

        $id_payment = $_POST['id_payment'] ?? $_GET['id_payment'] ?? null;
        if (!$id_payment) {
            $this->setFlash('error', 'ID pembayaran tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }

        $paymentModel = $this->model('Payment');
        $bookingModel = $this->model('Booking');

        $payments = $paymentModel->getByStatus('Pending');
        $targetPayment = null;
        foreach ($payments as $p) {
            if ($p['id_payment'] == $id_payment) { $targetPayment = $p; break; }
        }
        if (!$targetPayment) {
            $direct = $this->findPaymentById($id_payment);
            $targetPayment = $direct;
        }
        if (!$targetPayment) {
            $this->setFlash('error', 'Data pembayaran tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }

        $result = $paymentModel->verifyPayment($id_payment);
        if ($result['success']) {
            $bookingModel->updateStatus($targetPayment['id_booking'], 'Disetujui');
            $this->setFlash('success', 'Pembayaran #' . $id_payment . ' diverifikasi, booking disetujui');
        } else {
            $this->setFlash('error', 'Gagal verifikasi pembayaran: ' . $result['message']);
        }
        $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
    }

    public function rejectPayment() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
        $id_payment = $_POST['id_payment'] ?? $_GET['id_payment'] ?? null;
        if (!$id_payment) {
            $this->setFlash('error', 'ID pembayaran tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }
        $paymentModel = $this->model('Payment');
        $result = $paymentModel->rejectPayment($id_payment, 'Ditolak oleh admin');
        if ($result['success']) {
            $this->setFlash('success', 'Pembayaran #' . $id_payment . ' ditolak');
        } else {
            $this->setFlash('error', 'Gagal menolak pembayaran: ' . $result['message']);
        }
        $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
    }

    private function findPaymentById($id_payment) {
        require_once __DIR__ . '/../../config/Database.php';
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare('SELECT * FROM payments WHERE id_payment = :id LIMIT 1');
        $stmt->bindParam(':id', $id_payment, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function approveBooking() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }

        $id_booking = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_booking = $_POST['id_booking'] ?? null;
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id_booking = $_GET['id_booking'] ?? null;
        }

        if ($id_booking === null) {
            $this->setFlash('error', 'ID booking tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }

        try {
            $bookingModel = $this->model('Booking');
            $result = $bookingModel->updateStatus($id_booking, 'Disetujui');
            if ($result) {
                $this->setFlash('success', 'Booking #' . $id_booking . ' berhasil disetujui');
            } else {
                $this->setFlash('error', 'Gagal menyetujui booking #' . $id_booking);
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
    }
    
    public function rejectBooking() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }

        $id_booking = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_booking = $_POST['id_booking'] ?? null;
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id_booking = $_GET['id_booking'] ?? null;
        }

        if ($id_booking === null) {
            $this->setFlash('error', 'ID booking tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }

        try {
            $bookingModel = $this->model('Booking');
            $result = $bookingModel->updateStatus($id_booking, 'Dibatalkan');
            if ($result) {
                $this->setFlash('success', 'Booking #' . $id_booking . ' berhasil ditolak');
            } else {
                $this->setFlash('error', 'Gagal menolak booking #' . $id_booking);
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
    }
    
    public function users() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
        
        $userModel = $this->model('User');
        $allUsers = $userModel->all();
        
        $data = [
            'admin' => $_SESSION['admin'],
            'users' => $allUsers,
            'title' => 'Manajemen User'
        ];
        
        $this->view('admin/users', $data);
    }
    
    public function deleteUser() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_user = $_POST['id_user'] ?? 0;
            
            $userModel = $this->model('User');
            $result = $userModel->delete($id_user);
            
            if ($result) {
                $this->setFlash('success', 'User berhasil dihapus!');
            } else {
                $this->setFlash('error', 'Gagal menghapus user');
            }
            
            $this->redirect('/Studio-Music/public/index.php?url=admin/users');
        }
    }
    
    public function logout() {
        session_start();
        unset($_SESSION['admin']);
        unset($_SESSION['role']);
        session_destroy();
        $this->redirect('/Studio-Music/public/index.php?url=auth/login');
    }

    public function studios() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
        
        $studioModel = $this->model('Studio');
        $allStudios = $studioModel->all();
        
        $data = [
            'admin' => $_SESSION['admin'],
            'studios' => $allStudios,
            'title' => 'Kelola Studio'
        ];
        
        $this->view('admin/studios', $data);
    }
    
    public function addStudio() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
        
        $data = [
            'admin' => $_SESSION['admin'],
            'title' => 'Tambah Studio'
        ];
        
        $this->view('admin/studio_form', $data);
    }
    
    public function editStudio($id_studio = null) {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
        
        if (!$id_studio) {
            $this->setFlash('error', 'ID Studio tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/studios');
        }
        
        $studioModel = $this->model('Studio');
        $studio = $studioModel->findById($id_studio);
        
        if (!$studio) {
            $this->setFlash('error', 'Studio tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/studios');
        }
        
        $data = [
            'admin' => $_SESSION['admin'],
            'studio' => $studio,
            'title' => 'Edit Studio'
        ];
        
        $this->view('admin/studio_form', $data);
    }
    
    public function saveStudio() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/Studio-Music/public/index.php?url=admin/studios');
        }
        
        $id_studio = $_POST['id_studio'] ?? null;
        $studioData = [
            'nama_studio' => $_POST['nama_studio'] ?? '',
            'deskripsi' => $_POST['deskripsi'] ?? '',
            'harga_per_jam' => $_POST['harga_per_jam'] ?? 0,
            'fasilitas' => $_POST['fasilitas'] ?? '',
            'kapasitas' => $_POST['kapasitas'] ?? 0,
            'gambar' => $_POST['gambar'] ?? 'default-studio.jpg',
            'status_ketersediaan' => $_POST['status_ketersediaan'] ?? 'Tersedia'
        ];
        
        $studioModel = $this->model('Studio');
        
        if ($id_studio) {
            $result = $studioModel->updateStudio($id_studio, $studioData);
        } else {
            $result = $studioModel->createStudio($studioData);
        }
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('error', $result['message']);
        }
        
        $this->redirect('/Studio-Music/public/index.php?url=admin/studios');
    }
    
    public function deleteStudio() {
        if (!isset($_SESSION['admin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/Studio-Music/public/index.php?url=admin/studios');
        }
        
        $id_studio = $_POST['id_studio'] ?? 0;
        $studioModel = $this->model('Studio');
        
        if ($studioModel->delete($id_studio)) {
            $this->setFlash('success', 'Studio berhasil dihapus');
        } else {
            $this->setFlash('error', 'Gagal menghapus studio');
        }
        
        $this->redirect('/Studio-Music/public/index.php?url=admin/studios');
    }
}
?>
