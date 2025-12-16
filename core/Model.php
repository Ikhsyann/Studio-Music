<?php

require_once __DIR__ . '/QueryBuilder.php';

// Kelas Model Dasar - Menyediakan fungsionalitas seperti ORM
class Model {
    protected $db, $table, $primaryKey = 'id';
    
    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = (new Database())->getConnection(); // Koneksi ke database
    }
    
    // Get QueryBuilder instance untuk query yang lebih kompleks
    protected function query() {
        return new QueryBuilder($this->db);
    }
    
    // Ambil semua record dari tabel - MENGGUNAKAN QUERY BUILDER
    public function all() {
        return $this->query()
                    ->table($this->table)
                    ->get();
    }
    
    // Cari single record berdasarkan ID - MENGGUNAKAN QUERY BUILDER
    public function find($id) {
        return $this->query()
                    ->table($this->table)
                    ->where($this->primaryKey, $id)
                    ->first();
    }
    
    // Insert record baru - MENGGUNAKAN QUERY BUILDER
    public function insert($data) {
        return $this->query()
                    ->table($this->table)
                    ->insert($data) > 0;
    }
    
    // Update record yang ada - MENGGUNAKAN QUERY BUILDER
    public function update($id, $data) {
        return $this->query()
                    ->table($this->table)
                    ->where($this->primaryKey, $id)
                    ->update($data) > 0;
    }
    
    // Hapus record berdasarkan ID - MENGGUNAKAN QUERY BUILDER
    public function delete($id) {
        return $this->query()
                    ->table($this->table)
                    ->where($this->primaryKey, $id)
                    ->delete() > 0;
    }
    
    // Validasi input data sesuai rules
    protected function validate($data, $rules) {
        $errors = [];
        $validators = [
            'required' => fn($v) => empty($v) ? "wajib diisi" : null,
            'email' => fn($v) => !filter_var($v, FILTER_VALIDATE_EMAIL) ? "harus berupa email yang valid" : null,
            'numeric' => fn($v) => !is_numeric($v) ? "harus berupa angka" : null
        ];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? '';
            foreach ($validators as $type => $validator) {
                if (strpos($rule, $type) !== false && ($type === 'required' || !empty($value))) {
                    if ($error = $validator($value)) {
                        $errors[$field] = ucfirst($field) . " $error";
                        break;
                    }
                }
            }
        }
        return $errors;
    }
}
?>
