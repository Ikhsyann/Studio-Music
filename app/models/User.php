<?php

require_once __DIR__ . '/../../core/Model.php';

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    
    public function register($data) {
        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'no_telp' => 'required|numeric'
        ];
        
        $errors = $this->validate($data, $rules);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        if ($this->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email sudah terdaftar'];
        }
        
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        if ($this->insert($data)) {
            return ['success' => true, 'message' => 'Registrasi berhasil'];
        }
        
        return ['success' => false, 'message' => 'Registrasi gagal'];
    }
    
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return ['success' => true, 'data' => $user];
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
    
    public function updateProfile($id_user, $data) {
        $rules = [
            'nama' => 'required',
            'no_telp' => 'required|numeric'
        ];
        
        $errors = $this->validate($data, $rules);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET nama = :nama, no_telp = :no_telp 
                  WHERE id_user = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nama', $data['nama']);
        $stmt->bindParam(':no_telp', $data['no_telp']);
        $stmt->bindParam(':id', $id_user, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Profile berhasil diupdate'];
        }
        
        return ['success' => false, 'message' => 'Gagal mengupdate profile'];
    }
}
?>
