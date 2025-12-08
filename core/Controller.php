<?php

class Controller {
    public function view($view, $data = []) {
        extract($data);
        
        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            // Provide `old()` helper to views for preserving form input after redirects
            if (!function_exists('old')) {
                function old($key, $default = '') {
                    if (session_status() === PHP_SESSION_NONE) {
                        @session_start();
                    }
                    if (isset($_SESSION['old']) && array_key_exists($key, $_SESSION['old'])) {
                        return $_SESSION['old'][$key];
                    }
                    return $default;
                }
            }

            // Provide helper to clear old inputs (call this after rendering if desired)
            if (!function_exists('clear_old')) {
                function clear_old() {
                    if (session_status() === PHP_SESSION_NONE) {
                        @session_start();
                    }
                    if (isset($_SESSION['old'])) unset($_SESSION['old']);
                }
            }

            require_once $viewFile;

            // Clear old inputs after rendering so they don't persist beyond the next request
            if (function_exists('clear_old')) {
                clear_old();
            }
        } else {
            echo "View not found: " . $view;
        }
    }
    
    public function model($model) {
        $modelFile = __DIR__ . '/../app/models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("Model not found: " . $model);
        }
    }
    
    public function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    public function setFlash($type, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];

        // If this is an error flash and the request had POST data, preserve it for repopulating forms
        if ($type === 'error' && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // store POST data (exclude passwords for safety)
            $old = $_POST;
            if (isset($old['password'])) unset($old['password']);
            if (isset($old['password_confirm'])) unset($old['password_confirm']);
            $_SESSION['old'] = $old;
        }
    }
    
    public function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
?>
