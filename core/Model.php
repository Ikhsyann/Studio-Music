<?php

// Base Model Class - Provides ORM-like functionality
class Model {
    protected $db, $table, $primaryKey = 'id';
    
    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = (new Database())->getConnection(); // Connect to database
    }
    
    // Get all records from table
    public function all() {
        return $this->db->query("SELECT * FROM {$this->table}")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Find single record by ID
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Insert new record
    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        
        foreach ($data as $key => $value) $stmt->bindValue(":$key", $value);
        return $stmt->execute();
    }
    
    // Update existing record
    public function update($id, $data) {
        $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = :id");
        
        foreach ($data as $key => $value) $stmt->bindValue(":$key", $value);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Delete record by ID
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    // Validate input data against rules
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
