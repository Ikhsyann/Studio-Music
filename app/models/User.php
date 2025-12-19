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
    
    // Login user dengan email dan password - MENGGUNAKAN QUERY BUILDER
    public function login($email, $password) {
        // Menggunakan Query Builder dengan method chaining
        $user = $this->query()
                     ->table($this->table)
                     ->where('email', '=', $email)
                     ->first();
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $this->response(true, '', $user);
        }
        
        return $this->response(false, 'Email atau password salah');
    }
    
    // Cari user berdasarkan email - MENGGUNAKAN QUERY BUILDER
    public function findByEmail($email) {
        return $this->query()
                    ->table($this->table)
                    ->where('email', $email)
                    ->first();
    }
    
    // Update profil user - MENGGUNAKAN QUERY BUILDER
    public function updateProfile($id_user, $data) {
        $errors = $this->validate($data, [
            'nama' => 'required',
            'no_telp' => 'required|numeric'
        ]);
        
        if ($errors) return $this->response(false, $errors);
        
        // Menggunakan Query Builder untuk UPDATE
        $affected = $this->query()
                         ->table($this->table)
                         ->where('id_user', $id_user)
                         ->update([
                             'nama' => $data['nama'],
                             'no_telp' => $data['no_telp']
                         ]);
        
        return $affected > 0
            ? $this->response(true, 'Profile berhasil diupdate')
            : $this->response(false, 'Gagal mengupdate profile');
    }
    
    // Update user oleh admin (bisa update email dan password)
    public function updateUser($id_user, $data) {
        $errors = $this->validate($data, [
            'nama' => 'required',
            'email' => 'required|email',
            'no_telp' => 'required|numeric'
        ]);
        
        if ($errors) return $this->response(false, $errors);
        
        // Ambil data user saat ini
        $currentUser = $this->find($id_user);
        if (!$currentUser) {
            return $this->response(false, 'User tidak ditemukan');
        }
        
        // Cek apakah email berubah
        if ($data['email'] !== $currentUser['email']) {
            // Email berubah, cek apakah sudah digunakan user lain
            $existingUser = $this->findByEmail($data['email']);
            if ($existingUser) {
                return $this->response(false, 'Email sudah digunakan user lain');
            }
        }
        
        $updateData = [
            'nama' => $data['nama'],
            'email' => $data['email'],
            'no_telp' => $data['no_telp']
        ];
        
        // Update password hanya jika diisi
        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $affected = $this->query()
                         ->table($this->table)
                         ->where('id_user', $id_user)
                         ->update($updateData);
        
        return $this->response(true, 'User berhasil diupdate');
    }
}
?>
