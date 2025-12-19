<?php

require_once __DIR__ . '/../../core/Controller.php';

// Controller untuk halaman admin (kelola booking, studio, user)
class AdminController extends Controller {
    
    public function __construct() {
        $this->ensureSession(); // Pastikan session aktif
    }
    
    // Validasi apakah user adalah admin
    private function checkAdmin() {
        if (!isset($_SESSION['admin']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
    }
    
    // Tampilkan dashboard admin dengan statistik
    public function dashboard() {
        $this->checkAdmin();
        
        $bookingModel = $this->model('Booking');
        
        $this->view('admin/dashboard', [
            'admin' => $_SESSION['admin'],
            'totalBookings' => count($bookingModel->all()),
            'totalStudios' => count($this->model('Studio')->all()),
            'totalUsers' => count($this->model('User')->all()),
            'bookings' => $this->model('Admin')->getAllBookings(),
            'title' => 'Admin Dashboard'
        ]);
    }

    // Verifikasi pembayaran: update status payment dan booking
    public function verifyPayment() {
        $this->checkAdmin();
        $id_payment = $_POST['id_payment'] ?? $_GET['id_payment'] ?? null;
        if (!$id_payment) {
            $this->setFlash('error', 'ID pembayaran tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }

        $paymentModel = $this->model('Payment');
        $targetPayment = null;
        foreach ($paymentModel->getByStatus('Pending') as $p) {
            if ($p['id_payment'] == $id_payment) { $targetPayment = $p; break; }
        }
        if (!$targetPayment) $targetPayment = $this->findPaymentById($id_payment);
        
        if (!$targetPayment) {
            $this->setFlash('error', 'Data pembayaran tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }

        $result = $paymentModel->verifyPayment($id_payment);
        if ($result['success']) {
            $this->model('Booking')->updateStatus($targetPayment['id_booking'], 'Disetujui');
            $this->setFlash('success', 'Pembayaran #' . $id_payment . ' diverifikasi, booking disetujui');
        } else {
            $this->setFlash('error', 'Gagal verifikasi pembayaran: ' . $result['message']);
        }
        $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
    }

    // Tolak pembayaran
    public function rejectPayment() {
        $this->checkAdmin();
        if (!($id_payment = $_POST['id_payment'] ?? $_GET['id_payment'] ?? null)) {
            $this->setFlash('error', 'ID pembayaran tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }
        
        $result = $this->model('Payment')->rejectPayment($id_payment, 'Ditolak oleh admin');
        $this->setFlash($result['success'] ? 'success' : 'error', 
                       $result['success'] ? 'Pembayaran #' . $id_payment . ' ditolak' : 'Gagal menolak pembayaran: ' . $result['message']);
        $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
    }

    // Helper: cari payment berdasarkan ID
    private function findPaymentById($id_payment) {
        require_once __DIR__ . '/../../config/Database.php';
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare('SELECT * FROM payments WHERE id_payment = :id LIMIT 1');
        $stmt->bindParam(':id', $id_payment, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Approve booking oleh admin
    public function approveBooking() {
        $this->checkAdmin();
        if (!($id_booking = $_POST['id_booking'] ?? $_GET['id_booking'] ?? null)) {
            $this->setFlash('error', 'ID booking tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }

        try {
            $result = $this->model('Booking')->updateStatus($id_booking, 'Disetujui', $_SESSION['admin']['id_admin']);
            $this->setFlash($result ? 'success' : 'error', $result ? 'Booking #' . $id_booking . ' berhasil disetujui' : 'Gagal menyetujui booking #' . $id_booking);
        } catch (Exception $e) {
            $this->setFlash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
    }
    
    // Reject booking oleh admin
    public function rejectBooking() {
        $this->checkAdmin();
        if (!($id_booking = $_POST['id_booking'] ?? $_GET['id_booking'] ?? null)) {
            $this->setFlash('error', 'ID booking tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
        }

        try {
            $result = $this->model('Booking')->updateStatus($id_booking, 'Dibatalkan', $_SESSION['admin']['id_admin']);
            $this->setFlash($result ? 'success' : 'error', $result ? 'Booking #' . $id_booking . ' berhasil ditolak' : 'Gagal menolak booking #' . $id_booking);
        } catch (Exception $e) {
            $this->setFlash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
    }
    
    // Tampilkan halaman manajemen user
    public function users() {
        $this->checkAdmin();
        $this->view('admin/users', [
            'admin' => $_SESSION['admin'],
            'users' => $this->model('User')->all(),
            'admins' => $this->model('Admin')->all(),
            'title' => 'Manajemen User'
        ]);
    }
    
    // Hapus user
    public function deleteUser() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->model('User')->delete($_POST['id_user'] ?? 0);
            $this->setFlash($result ? 'success' : 'error', $result ? 'User berhasil dihapus!' : 'Gagal menghapus user');
            $this->redirect('/Studio-Music/public/index.php?url=admin/users');
        }
    }
    
    // Hapus admin
    public function deleteAdmin() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->model('Admin')->delete($_POST['id_admin'] ?? 0);
            $this->setFlash($result ? 'success' : 'error', $result ? 'Admin berhasil dihapus!' : 'Gagal menghapus admin');
            $this->redirect('/Studio-Music/public/index.php?url=admin/users');
        }
    }
    
    // Logout admin
    public function logout() {
        session_destroy();
        $this->redirect('/Studio-Music/public/index.php?url=auth/login');
    }

    // Tampilkan halaman manajemen studio
    public function studios() {
        $this->checkAdmin();
        $this->view('admin/studios', [
            'admin' => $_SESSION['admin'],
            'studios' => $this->model('Studio')->all(),
            'title' => 'Kelola Studio'
        ]);
    }
    
    // Tampilkan form tambah studio
    public function addStudio() {
        $this->checkAdmin();
        $this->view('admin/studio_form', ['admin' => $_SESSION['admin'], 'title' => 'Tambah Studio']);
    }
    
    // Tampilkan form edit studio
    public function editStudio($id_studio = null) {
        $this->checkAdmin();
        if (!$id_studio || !($studio = $this->model('Studio')->findById($id_studio))) {
            $this->setFlash('error', $id_studio ? 'Studio tidak ditemukan' : 'ID Studio tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/studios');
        }
        
        $this->view('admin/studio_form', ['admin' => $_SESSION['admin'], 'studio' => $studio, 'title' => 'Edit Studio']);
    }
    
    // Simpan studio (create atau update) dengan upload gambar
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

        // Validasi server-side: deskripsi maksimal 150 karakter
        if (isset($studioData['deskripsi']) && mb_strlen($studioData['deskripsi']) > 150) {
            $this->setFlash('error', 'Deskripsi tidak boleh lebih dari 150 karakter.');
            $this->redirect($redirectToEdit);
            return;
        }

        // Tangani upload gambar (nama file input: gambar_file)
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

            // Buat slug dari nama studio untuk nama file
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($studioData['nama_studio'])));
            $slug = trim($slug, '-');
            if (empty($slug)) {
                $slug = 'studio';
            }

            // Pastikan nama file unik: coba slug.ext, jika ada tambahkan timestamp
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

            // jika edit dan ada gambar sebelumnya (dan bukan default), hapus gambar lama
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
    
    // Hapus studio
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
    
    // Tampilkan form tambah user
    public function addUser() {
        $this->checkAdmin();
        $this->view('admin/user_form', ['admin' => $_SESSION['admin'], 'title' => 'Tambah User', 'isEdit' => false]);
    }
    
    // Tampilkan form edit user
    public function editUser($id_user = null) {
        $this->checkAdmin();
        
        if (!$id_user) {
            $this->setFlash('error', 'ID User tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/users');
            return;
        }
        
        $user = $this->model('User')->find($id_user);
        
        if (!$user) {
            $this->setFlash('error', 'User tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=admin/users');
            return;
        }
        
        $this->view('admin/user_form', [
            'admin' => $_SESSION['admin'], 
            'title' => 'Edit User',
            'user' => $user,
            'isEdit' => true
        ]);
    }
    
    // Tampilkan form tambah admin
    public function addAdmin() {
        $this->checkAdmin();
        $this->view('admin/admin_form', ['admin' => $_SESSION['admin'], 'title' => 'Tambah Admin']);
    }
    
    // Simpan user baru atau update user
    public function saveUser() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/Studio-Music/public/index.php?url=admin/users');
            return;
        }
        
        $id_user = $_POST['id_user'] ?? null;
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $no_telp = trim($_POST['no_telp'] ?? '');
        
        $errors = [];
        
        if (strlen($nama) < 3) $errors[] = 'Nama minimal 3 karakter';
        if (!preg_match('/^[A-Za-z\s]+$/', $nama)) $errors[] = 'Nama hanya boleh berisi huruf dan spasi';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid';
        if (!preg_match('/^[0-9]{10,15}$/', $no_telp)) $errors[] = 'Nomor telepon harus 10-15 digit angka';
        
        // Validasi password hanya untuk user baru atau jika password diisi saat edit
        if (!$id_user) {
            // Create mode - password wajib
            if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
            if ($password !== $confirm_password) $errors[] = 'Password dan Konfirmasi Password tidak sama';
        } else {
            // Edit mode - password optional
            if (!empty($password)) {
                if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
                if ($password !== $confirm_password) $errors[] = 'Password dan Konfirmasi Password tidak sama';
            }
        }
        
        if ($errors) {
            $this->setFlash('error', implode(', ', $errors));
            $this->redirect($id_user ? '/Studio-Music/public/index.php?url=admin/editUser/' . $id_user : '/Studio-Music/public/index.php?url=admin/addUser');
            return;
        }
        
        if ($id_user) {
            // Update existing user
            $result = $this->model('User')->updateUser($id_user, [
                'nama' => $nama,
                'email' => $email,
                'password' => $password,
                'no_telp' => $no_telp
            ]);
            $this->setFlash($result['success'] ? 'success' : 'error', $result['message']);
        } else {
            // Create new user
            $result = $this->model('User')->register(['nama' => $nama, 'email' => $email, 'password' => $password, 'no_telp' => $no_telp]);
            $this->setFlash($result['success'] ? 'success' : 'error', $result['success'] ? 'User berhasil ditambahkan' : ($result['message'] ?? 'Gagal menambahkan user'));
        }
        
        $this->redirect('/Studio-Music/public/index.php?url=admin/users');
    }
    
    // Simpan admin baru
    public function saveAdmin() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') $this->redirect('/Studio-Music/public/index.php?url=admin/users');
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $errors = array_merge(
            !filter_var($email, FILTER_VALIDATE_EMAIL) ? ['Format email tidak valid'] : [],
            strlen($password) < 6 ? ['Password minimal 6 karakter'] : []
        );
        
        if ($errors) {
            $this->setFlash('error', implode(', ', $errors));
            $this->redirect('/Studio-Music/public/index.php?url=admin/addAdmin');
            return;
        }
        
        $result = $this->model('Admin')->createAdmin($email, $password);
        $this->setFlash($result['success'] ? 'success' : 'error', $result['success'] ? 'Admin berhasil ditambahkan' : $result['message']);
        $this->redirect('/Studio-Music/public/index.php?url=admin/users');
    }
}
?>
