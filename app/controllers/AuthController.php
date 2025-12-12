<?php

require_once __DIR__ . '/../../core/Controller.php';

// Controller untuk autentikasi (login, register, logout)
class AuthController extends Controller {
    
    public function __construct() {
        $this->ensureSession(); // Pastikan session aktif
    }
    
    // Redirect ke dashboard jika sudah login
    private function redirectIfLoggedIn() {
        if (isset($_SESSION['user'])) {
            $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
        }
    }
    
    // Tampilkan halaman login
    public function login() {
        $this->redirectIfLoggedIn();
        $this->view('auth/login');
    }
    
    // Proses login (cek admin dulu, lalu user)
    public function loginProcess() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $adminResult = $this->model('Admin')->login($email, $password);
            
            if ($adminResult['success']) {
                $_SESSION = array_merge($_SESSION, ['admin' => $adminResult['data'], 'role' => 'admin']);
                $this->setFlash('success', 'Login berhasil sebagai Admin!');
                $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
                return;
            }
            
            $userResult = $this->model('User')->login($email, $password);
            
            if ($userResult['success']) {
                $_SESSION = array_merge($_SESSION, ['user' => $userResult['data'], 'role' => 'user']);
                $this->setFlash('success', 'Login berhasil!');
                $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
                return;
            }
            
            $this->setFlash('error', 'Email atau password salah');
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
    }
    
    // Tampilkan halaman register
    public function register() {
        $this->redirectIfLoggedIn();
        $this->view('auth/register');
    }
    
    // Proses registrasi user baru dengan validasi
    public function registerProcess() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = ['nama' => trim($_POST['nama'] ?? ''), 'email' => trim($_POST['email'] ?? ''),
                     'password' => $_POST['password'] ?? '', 'no_telp' => trim($_POST['no_telp'] ?? '')];
            
            $errors = array_merge(
                strlen($data['nama']) < 3 ? ['Nama minimal 3 karakter'] : [],
                !preg_match('/^[A-Za-z\s]+$/', $data['nama']) ? ['Nama hanya boleh berisi huruf dan spasi'] : [],
                !filter_var($data['email'], FILTER_VALIDATE_EMAIL) ? ['Format email tidak valid'] : [],
                !preg_match('/^[0-9]{10,15}$/', $data['no_telp']) ? ['Nomor telepon harus 10-15 digit angka'] : [],
                strlen($data['password']) < 6 ? ['Password minimal 6 karakter'] : []
            );
            
            if ($errors) {
                $this->setFlash('error', implode(', ', $errors));
                $this->redirect('/Studio-Music/public/index.php?url=auth/register');
                return;
            }
            
            $result = $this->model('User')->register($data);
            
            $this->setFlash($result['success'] ? 'success' : 'error', 
                           $result['success'] ? 'Registrasi berhasil! Silakan login.' : ($result['message'] ?? 'Registrasi gagal'));
            $this->redirect('/Studio-Music/public/index.php?url=auth/' . ($result['success'] ? 'login' : 'register'));
        }
    }
    
    // Logout dan hapus semua session
    public function logout() {
        session_destroy();
        $this->redirect('/Studio-Music/public/index.php?url=auth/login');
    }
}
?>
