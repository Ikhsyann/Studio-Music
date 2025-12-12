<?php

require_once __DIR__ . '/../../core/Model.php';

// Model User - Mengelola data user dan autentikasi
class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    
    // Format response dengan optional data
    private function response($success, $messageOrErrors, $data = null) {
        return array_merge(['success' => $success], 
            is_array($messageOrErrors) ? ['errors' => $messageOrErrors] : ['message' => $messageOrErrors],
            $data ? ['data' => $data] : []
        );
    }
    
    // Register user baru
    public function register($data) {
        $errors = $this->validate($data, [
            'nama' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'no_telp' => 'required|numeric'
        ]);
        
        if ($errors) return $this->response(false, $errors);
        if ($this->findByEmail($data['email'])) return $this->response(false, 'Email sudah terdaftar');
        
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $this->insert($data)
            ? $this->response(true, 'Registrasi berhasil')
            : $this->response(false, 'Registrasi gagal');
    }
    
    // Login user dengan email dan password
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $this->response(true, '', $user);
        }
        
        return $this->response(false, 'Email atau password salah');
    }
    
    // Cari user berdasarkan email
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Update profil user
    public function updateProfile($id_user, $data) {
        $errors = $this->validate($data, [
            'nama' => 'required',
            'no_telp' => 'required|numeric'
        ]);
        
        if ($errors) return $this->response(false, $errors);
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET nama = ?, no_telp = ? WHERE id_user = ?");
        
        return $stmt->execute([$data['nama'], $data['no_telp'], $id_user])
            ? $this->response(true, 'Profile berhasil diupdate')
            : $this->response(false, 'Gagal mengupdate profile');
    }
}
?>
