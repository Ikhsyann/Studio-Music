<?php

class Controller {
    public function view($view, $data = []) {
        extract($data);
        
        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
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
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
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
