<?php

require_once __DIR__ . '/../../core/Model.php';

// Model Admin - Mengelola data admin dan operasi admin
class Admin extends Model {
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    
    // Format response dengan optional data
    private function response($success, $message, $data = null) {
        return array_merge(['success' => $success, 'message' => $message], $data ? ['data' => $data] : []);
    }
    
    // Login admin dengan email dan password
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password'])) {
            unset($admin['password']);
            return $this->response(true, '', $admin);
        }
        
        return $this->response(false, 'Email atau password salah');
    }
    
    // Cari admin berdasarkan email
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Ambil semua data user
    public function getAllUsers() {
        $stmt = $this->db->prepare("SELECT id_user, nama, email, no_telp, created_at FROM users ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ambil semua booking dengan detail lengkap
    public function getAllBookings() {
        $stmt = $this->db->prepare(
            "SELECT b.*, s.nama_studio, u.nama as nama_user, u.email as email_user, 
                    p.bukti_pembayaran, a.email as admin_email
             FROM booking b
             JOIN studios s ON b.id_studio = s.id_studio
             JOIN users u ON b.id_user = u.id_user
             LEFT JOIN payments p ON b.id_booking = p.id_booking
             LEFT JOIN admin a ON b.id_admin = a.id_admin
             ORDER BY b.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Hapus user berdasarkan ID
    public function deleteUser($id_user) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id_user = ?");
        return $stmt->execute([$id_user]);
    }
    
    // Buat admin baru
    public function createAdmin($email, $password) {
        if ($this->findByEmail($email)) return $this->response(false, 'Email sudah digunakan');
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (email, password) VALUES (?, ?)");
        
        return $stmt->execute([$email, password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])])
            ? $this->response(true, 'Admin berhasil dibuat')
            : $this->response(false, 'Gagal membuat admin');
    }
}
?>
