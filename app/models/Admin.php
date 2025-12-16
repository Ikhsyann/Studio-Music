<?php

require_once __DIR__ . '/../../core/Model.php';

// Model Admin - Mengelola data admin dan operasi admin
class Admin extends Model {
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    
    // Format response dengan data opsional
    private function response($success, $message, $data = null) {
        return array_merge(['success' => $success, 'message' => $message], $data ? ['data' => $data] : []);
    }
    
    // Login admin dengan email dan password - MENGGUNAKAN QUERY BUILDER
    public function login($email, $password) {
        $admin = $this->query()
                      ->table($this->table)
                      ->where('email', $email)
                      ->first();
        
        if ($admin && password_verify($password, $admin['password'])) {
            unset($admin['password']);
            return $this->response(true, '', $admin);
        }
        
        return $this->response(false, 'Email atau password salah');
    }
    
    // Cari admin berdasarkan email - MENGGUNAKAN QUERY BUILDER
    public function findByEmail($email) {
        return $this->query()
                    ->table($this->table)
                    ->where('email', $email)
                    ->first();
    }
    
    // Ambil semua data user - MENGGUNAKAN QUERY BUILDER
    public function getAllUsers() {
        return $this->query()
                    ->table('users')
                    ->select(['id_user', 'nama', 'email', 'no_telp', 'created_at'])
                    ->orderBy('created_at', 'DESC')
                    ->get();
    }
    
    // Ambil semua booking dengan detail lengkap - MENGGUNAKAN QUERY BUILDER
    public function getAllBookings() {
        return $this->query()
                    ->table('booking b')
                    ->select('b.*, s.nama_studio, u.nama as nama_user, u.email as email_user, p.bukti_pembayaran, a.email as admin_email')
                    ->join('studios s', 'b.id_studio', '=', 's.id_studio')
                    ->join('users u', 'b.id_user', '=', 'u.id_user')
                    ->leftJoin('payments p', 'b.id_booking', '=', 'p.id_booking')
                    ->leftJoin('admin a', 'b.id_admin', '=', 'a.id_admin')
                    ->orderBy('b.created_at', 'DESC')
                    ->get();
    }
    
    // Hapus user berdasarkan ID - MENGGUNAKAN QUERY BUILDER
    public function deleteUser($id_user) {
        return $this->query()
                    ->table('users')
                    ->where('id_user', $id_user)
                    ->delete() > 0;
    }
    
    // Buat admin baru - MENGGUNAKAN QUERY BUILDER
    public function createAdmin($email, $password) {
        if ($this->findByEmail($email)) return $this->response(false, 'Email sudah digunakan');
        
        $insertId = $this->query()
                         ->table($this->table)
                         ->insert([
                             'email' => $email,
                             'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])
                         ]);
        
        return $insertId > 0
            ? $this->response(true, 'Admin berhasil dibuat')
            : $this->response(false, 'Gagal membuat admin');
    }
}
?>
