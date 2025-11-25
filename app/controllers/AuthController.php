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
            $data = [
                'nama' => $_POST['nama'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'no_telp' => $_POST['no_telp'] ?? ''
            ];
            
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
        session_start();
        session_destroy();
        $this->redirect('/Studio-Music/public/index.php?url=auth/login');
    }
}
?>
