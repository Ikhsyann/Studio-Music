<?php

// Kelas Controller Dasar - Menangani views, models, redirects, dan flash messages
class Controller {
    // Pastikan session sudah dimulai
    protected function ensureSession() {
        if (session_status() === PHP_SESSION_NONE) @session_start();
    }
    
    // Render view dengan data
    public function view($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . "/../app/views/$view.php";
        
        if (!file_exists($viewFile)) die("View tidak ditemukan: $view");
        
        // Fungsi helper untuk mengambil input form lama
        if (!function_exists('old')) {
            function old($key, $default = '') {
                if (session_status() === PHP_SESSION_NONE) @session_start();
                return $_SESSION['old'][$key] ?? $default;
            }
        }
        
        require_once $viewFile;
        unset($_SESSION['old']); // Hapus data lama setelah render
    }
    
    // Muat instance model
    public function model($model) {
        $file = __DIR__ . "/../app/models/$model.php";
        if (!file_exists($file)) die("Model tidak ditemukan: $model");
        require_once $file;
        return new $model();
    }
    
    // Redirect ke URL
    public function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    // Set flash message untuk request berikutnya
    public function setFlash($type, $message) {
        $this->ensureSession();
        $_SESSION['flash'] = compact('type', 'message');
        
        // Simpan data POST saat error (kecuali password)
        if ($type === 'error' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['old'] = array_diff_key($_POST, array_flip(['password', 'password_confirm']));
        }
    }
    
    // Ambil dan hapus flash message
    public function getFlash() {
        $this->ensureSession();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}
?>
