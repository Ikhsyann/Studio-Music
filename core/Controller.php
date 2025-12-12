<?php

// Base Controller Class - Handles views, models, redirects, and flash messages
class Controller {
    // Ensure session is started
    protected function ensureSession() {
        if (session_status() === PHP_SESSION_NONE) @session_start();
    }
    
    // Render view with data
    public function view($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . "/../app/views/$view.php";
        
        if (!file_exists($viewFile)) die("View not found: $view");
        
        // Helper function to retrieve old form input
        if (!function_exists('old')) {
            function old($key, $default = '') {
                if (session_status() === PHP_SESSION_NONE) @session_start();
                return $_SESSION['old'][$key] ?? $default;
            }
        }
        
        require_once $viewFile;
        unset($_SESSION['old']); // Clear old data after render
    }
    
    // Load model instance
    public function model($model) {
        $file = __DIR__ . "/../app/models/$model.php";
        if (!file_exists($file)) die("Model not found: $model");
        require_once $file;
        return new $model();
    }
    
    // Redirect to URL
    public function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    // Set flash message for next request
    public function setFlash($type, $message) {
        $this->ensureSession();
        $_SESSION['flash'] = compact('type', 'message');
        
        // Preserve POST data on error (exclude passwords)
        if ($type === 'error' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['old'] = array_diff_key($_POST, array_flip(['password', 'password_confirm']));
        }
    }
    
    // Get and clear flash message
    public function getFlash() {
        $this->ensureSession();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}
?>
