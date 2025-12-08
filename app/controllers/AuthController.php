<?php

require_once __DIR__ . '/../../core/Controller.php';

class AuthController extends Controller {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function login() {
        if (isset($_SESSION['user'])) {
            $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
        }
        
        $this->view('auth/login');
    }
    
    public function loginProcess() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $adminModel = $this->model('Admin');
            $adminResult = $adminModel->login($email, $password);
            
            if ($adminResult['success']) {
                $_SESSION['admin'] = $adminResult['data'];
                $_SESSION['role'] = 'admin';
                $this->setFlash('success', 'Login berhasil sebagai Admin!');
                $this->redirect('/Studio-Music/public/index.php?url=admin/dashboard');
                return;
            }
            
            $userModel = $this->model('User');
            $userResult = $userModel->login($email, $password);
            
            if ($userResult['success']) {
                $_SESSION['user'] = $userResult['data'];
                $_SESSION['role'] = 'user';
                $this->setFlash('success', 'Login berhasil!');
                $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
                return;
            }
            
            $this->setFlash('error', 'Email atau password salah');
            $this->redirect('/Studio-Music/public/index.php?url=auth/login');
        }
    }
    
    public function register() {
        if (isset($_SESSION['user'])) {
            $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
        }
        
        $this->view('auth/register');
    }
    
    public function registerProcess() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Remove confirm_password, it's only for client-side validation
            $data = [
                'nama' => trim($_POST['nama'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'no_telp' => trim($_POST['no_telp'] ?? '')
            ];
            
            // Server-side validation
            $errors = [];
            
            // Validate nama
            if (empty($data['nama']) || strlen($data['nama']) < 3) {
                $errors[] = 'Nama minimal 3 karakter';
            }
            if (!preg_match('/^[A-Za-z\s]+$/', $data['nama'])) {
                $errors[] = 'Nama hanya boleh berisi huruf dan spasi';
            }
            
            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format email tidak valid';
            }
            
            // Validate phone
            if (!preg_match('/^[0-9]{10,15}$/', $data['no_telp'])) {
                $errors[] = 'Nomor telepon harus 10-15 digit angka';
            }
            
            // Validate password
            if (strlen($data['password']) < 6) {
                $errors[] = 'Password minimal 6 karakter';
            }
            
            if (!empty($errors)) {
                $this->setFlash('error', implode(', ', $errors));
                $this->redirect('/Studio-Music/public/index.php?url=auth/register');
                return;
            }
            
            $userModel = $this->model('User');
            $result = $userModel->register($data);
            
            if ($result['success']) {
                $this->setFlash('success', 'Registrasi berhasil! Silakan login.');
                $this->redirect('/Studio-Music/public/index.php?url=auth/login');
            } else {
                $this->setFlash('error', $result['message'] ?? 'Registrasi gagal');
                $this->redirect('/Studio-Music/public/index.php?url=auth/register');
            }
        }
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('/Studio-Music/public/index.php?url=auth/login');
    }
}
?>
