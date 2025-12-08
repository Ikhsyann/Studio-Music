<?php

require_once __DIR__ . '/../../core/Model.php';

class Admin extends Model {
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password'])) {
            unset($admin['password']);
            return ['success' => true, 'data' => $admin];
        }
        
        return ['success' => false, 'message' => 'Email atau password salah'];
    }
    
    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllUsers() {
        $query = "SELECT id_user, nama, email, no_telp, created_at 
                  FROM users 
                  ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllBookings() {
        $query = "SELECT b.*, s.nama_studio, u.nama as nama_user, u.email as email_user,
                  p.bukti_pembayaran, a.email as admin_email
                  FROM booking b
                  JOIN studios s ON b.id_studio = s.id_studio
                  JOIN users u ON b.id_user = u.id_user
                  LEFT JOIN payments p ON b.id_booking = p.id_booking
                  LEFT JOIN admin a ON b.id_admin = a.id_admin
                  ORDER BY b.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteUser($id_user) {
        $query = "DELETE FROM users WHERE id_user = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id_user, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function createAdmin($email, $password) {
        $existing = $this->findByEmail($email);
        if ($existing) {
            return ['success' => false, 'message' => 'Email sudah digunakan'];
        }
        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $query = "INSERT INTO " . $this->table . " (email, password) VALUES (:email, :password)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Admin berhasil dibuat'];
        }
        
        return ['success' => false, 'message' => 'Gagal membuat admin'];
    }
}
?>
