<?php

require_once __DIR__ . '/../../core/Controller.php';

class AdminController extends Controller {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    private function checkAdmin() {
        if (!isset($_SESSION['admin']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
            exit;
        }
    }
    
    public function dashboard() {
        $this->checkAdmin();
        
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
        $this->checkAdmin();
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
        $this->checkAdmin();
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
        $this->checkAdmin();
        $id_booking = $_POST['id_booking'] ?? $_GET['id_booking'] ?? null;

        if ($id_booking === null) {
            $this->setFlash('error', 'ID booking tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }

        try {
            $id_admin = $_SESSION['admin']['id_admin'];
            $bookingModel = $this->model('Booking');
            $result = $bookingModel->updateStatus($id_booking, 'Disetujui', $id_admin);
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
        $this->checkAdmin();
        $id_booking = $_POST['id_booking'] ?? $_GET['id_booking'] ?? null;

        if ($id_booking === null) {
            $this->setFlash('error', 'ID booking tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }

        try {
            $id_admin = $_SESSION['admin']['id_admin'];
            $bookingModel = $this->model('Booking');
            $result = $bookingModel->updateStatus($id_booking, 'Dibatalkan', $id_admin);
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
        $this->checkAdmin();
        $userModel = $this->model('User');
        $allUsers = $userModel->all();
        
        $adminModel = $this->model('Admin');
        $allAdmins = $adminModel->all();
        
        $data = [
            'admin' => $_SESSION['admin'],
            'users' => $allUsers,
            'admins' => $allAdmins,
            'title' => 'Manajemen User'
        ];
        
        $this->view('admin/users', $data);
    }
    
    public function deleteUser() {
        $this->checkAdmin();
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
    
    public function deleteAdmin() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_admin = $_POST['id_admin'] ?? 0;
            
            $adminModel = $this->model('Admin');
            $result = $adminModel->delete($id_admin);
            
            if ($result) {
                $this->setFlash('success', 'Admin berhasil dihapus!');
            } else {
                $this->setFlash('error', 'Gagal menghapus admin');
            }
            
            $this->redirect('/Studio-Music/public/index.php?url=admin/users');
        }
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('/Studio-Music/public/index.php?url=auth/login');
    }

    public function studios() {
        $this->checkAdmin();
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
        $this->checkAdmin();
        $data = [
            'admin' => $_SESSION['admin'],
            'title' => 'Tambah Studio'
        ];
        
        $this->view('admin/studio_form', $data);
    }
    
    public function editStudio($id_studio = null) {
        $this->checkAdmin();
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
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/Studio-Music/public/index.php?url=admin/studios');
        }
        
        $id_studio = $_POST['id_studio'] ?? null;
        $redirectToAdd = '/Studio-Music/public/index.php?url=admin/addStudio';
        $redirectToEdit = $id_studio ? '/Studio-Music/public/index.php?url=admin/editStudio/' . $id_studio : $redirectToAdd;
        $studioData = [
            'nama_studio' => $_POST['nama_studio'] ?? '',
            'deskripsi' => $_POST['deskripsi'] ?? '',
            'harga_per_jam' => $_POST['harga_per_jam'] ?? 0,
            'fasilitas' => $_POST['fasilitas'] ?? '',
            'kapasitas' => $_POST['kapasitas'] ?? 0,
            // 'gambar' will be set after processing upload
            'status_ketersediaan' => $_POST['status_ketersediaan'] ?? 'Tersedia'
        ];

        // Server-side validation: deskripsi maksimal 150 karakter
        if (isset($studioData['deskripsi']) && mb_strlen($studioData['deskripsi']) > 150) {
            $this->setFlash('error', 'Deskripsi tidak boleh lebih dari 150 karakter.');
            $this->redirect($redirectToEdit);
            return;
        }

        // Handle gambar upload (file input name: gambar_file)
        $uploadDir = __DIR__ . '/../../public/images/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0775, true);
        }

        $finalFilename = $_POST['existing_gambar'] ?? 'default-studio.jpg';
        if (isset($_FILES['gambar_file']) && isset($_FILES['gambar_file']['error']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['gambar_file']['tmp_name'];
            $origName = $_FILES['gambar_file']['name'];
            $fileExt = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png'];

            if (!in_array($fileExt, $allowed)) {
                $this->setFlash('error', 'Format file tidak valid. Hanya JPG/JPEG/PNG.');
                $this->redirect($redirectToEdit);
                return;
            }

            if (filesize($tmpPath) > 5 * 1024 * 1024) {
                $this->setFlash('error', 'Ukuran file terlalu besar (max 5MB)');
                $this->redirect($redirectToEdit);
                return;
            }

            // Create slug from studio name for filename
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($studioData['nama_studio'])));
            $slug = trim($slug, '-');
            if (empty($slug)) {
                $slug = 'studio';
            }

            // Ensure unique filename: try slug.ext, if exists append timestamp
            $candidate = $slug . '.' . $fileExt;
            $finalPath = $uploadDir . $candidate;
            if (file_exists($finalPath)) {
                $candidate = $slug . '_' . time() . '.' . $fileExt;
                $finalPath = $uploadDir . $candidate;
            }

            if (!move_uploaded_file($tmpPath, $finalPath)) {
                $this->setFlash('error', 'Gagal menyimpan file gambar.');
                $this->redirect($redirectToEdit);
                return;
            }

            // if editing and there was an existing image (and not default), delete it
            if ($id_studio && !empty($_POST['existing_gambar']) && $_POST['existing_gambar'] !== 'default-studio.jpg') {
                $old = $uploadDir . basename($_POST['existing_gambar']);
                if (file_exists($old)) @unlink($old);
            }

            $finalFilename = $candidate;
        }

        $studioData['gambar'] = $finalFilename;
        
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
        $this->checkAdmin();
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
    
    public function addUser() {
        $this->checkAdmin();
        $data = [
            'admin' => $_SESSION['admin'],
            'title' => 'Tambah User'
        ];
        
        $this->view('admin/user_form', $data);
    }
    
    public function addAdmin() {
        $this->checkAdmin();
        $data = [
            'admin' => $_SESSION['admin'],
            'title' => 'Tambah Admin'
        ];
        
        $this->view('admin/admin_form', $data);
    }
    
    public function saveUser() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/Studio-Music/public/index.php?url=admin/users');
        }
        
        // Remove confirm_password, it's only for client-side validation
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $no_telp = trim($_POST['no_telp'] ?? '');
        
        // Server-side validation
        $errors = [];
        
        if (empty($nama) || strlen($nama) < 3) {
            $errors[] = 'Nama minimal 3 karakter';
        }
        if (!preg_match('/^[A-Za-z\s]+$/', $nama)) {
            $errors[] = 'Nama hanya boleh berisi huruf dan spasi';
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        }
        
        if (!preg_match('/^[0-9]{10,15}$/', $no_telp)) {
            $errors[] = 'Nomor telepon harus 10-15 digit angka';
        }
        
        if (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        }
        
        if (!empty($errors)) {
            $this->setFlash('error', implode(', ', $errors));
            $this->redirect('/Studio-Music/public/index.php?url=admin/addUser');
            return;
        }
        
        $userData = [
            'nama' => $nama,
            'email' => $email,
            'password' => $password,
            'no_telp' => $no_telp
        ];
        
        $userModel = $this->model('User');
        $result = $userModel->register($userData);
        
        if ($result['success']) {
            $this->setFlash('success', 'User berhasil ditambahkan');
        } else {
            $this->setFlash('error', $result['message'] ?? 'Gagal menambahkan user');
        }
        
        $this->redirect('/Studio-Music/public/index.php?url=admin/users');
    }
    
    public function saveAdmin() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/Studio-Music/public/index.php?url=admin/users');
        }
        
        // Remove confirm_password, it's only for client-side validation
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Server-side validation
        $errors = [];
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        }
        
        if (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        }
        
        if (!empty($errors)) {
            $this->setFlash('error', implode(', ', $errors));
            $this->redirect('/Studio-Music/public/index.php?url=admin/addAdmin');
            return;
        }
        
        $adminModel = $this->model('Admin');
        $result = $adminModel->createAdmin($email, $password);
        
        if ($result['success']) {
            $this->setFlash('success', 'Admin berhasil ditambahkan');
        } else {
            $this->setFlash('error', $result['message']);
        }
        
        $this->redirect('/Studio-Music/public/index.php?url=admin/users');
    }
}
?>
