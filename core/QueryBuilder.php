<?php

// Query Builder Class - Membangun SQL query dengan method chaining
class QueryBuilder {
    
    protected $pdo;
    protected $table;
    protected $select = '*';
    protected $joins = [];
    protected $wheres = [];
    protected $orderBy = [];
    protected $limit;
    protected $offset;
    protected $bindings = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Set table name
    public function table($table) {
        $this->table = $table;
        return $this;
    }
    
    // Set columns to select
    public function select($columns = '*') {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }
    
    // Add WHERE clause
    public function where($column, $operator, $value = null) {
        // Jika hanya 2 parameter, anggap operator adalah '='
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $placeholder = $this->generatePlaceholder($column);
        $this->wheres[] = "$column $operator :$placeholder";
        $this->bindings[$placeholder] = $value;
        
        return $this;
    }
    
    // Add WHERE IN clause
    public function whereIn($column, array $values) {
        $placeholders = [];
        foreach ($values as $i => $value) {
            $placeholder = $column . '_' . $i;
            $placeholders[] = ":$placeholder";
            $this->bindings[$placeholder] = $value;
        }
        
        $this->wheres[] = "$column IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }
    
    // Add WHERE NOT IN clause
    public function whereNotIn($column, array $values) {
        $placeholders = [];
        foreach ($values as $i => $value) {
            $placeholder = $column . '_' . $i;
            $placeholders[] = ":$placeholder";
            $this->bindings[$placeholder] = $value;
        }
        
        $this->wheres[] = "$column NOT IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }
    
    // Add OR WHERE clause
    public function orWhere($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $placeholder = $this->generatePlaceholder($column);
        
        // Jika sudah ada where, tambahkan OR
        if (!empty($this->wheres)) {
            $lastIndex = count($this->wheres) - 1;
            $this->wheres[$lastIndex] .= " OR $column $operator :$placeholder";
        } else {
            $this->wheres[] = "$column $operator :$placeholder";
        }
        
        $this->bindings[$placeholder] = $value;
        return $this;
    }
    
    // Add JOIN clause
    public function join($table, $first, $operator, $second) {
        $this->joins[] = "JOIN $table ON $first $operator $second";
        return $this;
    }
    
    // Add LEFT JOIN clause
    public function leftJoin($table, $first, $operator, $second) {
        $this->joins[] = "LEFT JOIN $table ON $first $operator $second";
        return $this;
    }
    
    // Add RIGHT JOIN clause
    public function rightJoin($table, $first, $operator, $second) {
        $this->joins[] = "RIGHT JOIN $table ON $first $operator $second";
        return $this;
    }
    
    // Add ORDER BY clause
    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy[] = "$column $direction";
        return $this;
    }
    
    // Set LIMIT
    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }
    
    // Set OFFSET
    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }
    
    // Execute query and get all results
    public function get() {
        $sql = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        
        $this->reset();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Execute query and get first result
    public function first() {
        $this->limit(1);
        $sql = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        
        $this->reset();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get count of records
    public function count() {
        $originalSelect = $this->select;
        $this->select = 'COUNT(*) as count';
        
        $sql = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->select = $originalSelect;
        $this->reset();
        
        return $result['count'] ?? 0;
    }
    
    // Insert data
    public function insert(array $data) {
        $columns = array_keys($data);
        $placeholders = array_map(function($col) { return ":$col"; }, $columns);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        
        $this->reset();
        return $this->pdo->lastInsertId();
    }
    
    // Update data
    public function update(array $data) {
        $sets = [];
        foreach ($data as $column => $value) {
            $sets[] = "$column = :update_$column";
            $this->bindings["update_$column"] = $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        
        $this->reset();
        return $stmt->rowCount();
    }
    
    // Delete data
    public function delete() {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        
        $this->reset();
        return $stmt->rowCount();
    }
    
    // Build SELECT query
    protected function buildSelectQuery() {
        $sql = "SELECT {$this->select} FROM {$this->table}";
        
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        return $sql;
    }
    
    // Generate unique placeholder name
    protected function generatePlaceholder($column) {
        static $counter = 0;
        $counter++;
        return str_replace('.', '_', $column) . '_' . $counter;
    }
    
    // Reset query builder
    protected function reset() {
        $this->select = '*';
        $this->joins = [];
        $this->wheres = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        $this->bindings = [];
    }
    
    // Get raw SQL (for debugging)
    public function toSql() {
        return $this->buildSelectQuery();
    }
}
?>
