<?php

require_once __DIR__ . '/../../core/Model.php';

// Model Studio - Mengelola data studio musik
class Studio extends Model {
    protected $table = 'studios';
    protected $primaryKey = 'id_studio';
    
    // Format response untuk return value
    private function response($success, $messageOrErrors) {
        return array_merge(['success' => $success], 
            is_array($messageOrErrors) ? ['errors' => $messageOrErrors] : ['message' => $messageOrErrors]
        );
    }
    
    // Ambil studio yang tersedia
    public function getAvailable() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status_ketersediaan = 'Tersedia' ORDER BY nama_studio");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Cari studio berdasarkan keyword
    public function search($keyword) {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} 
             WHERE (nama_studio LIKE ? OR deskripsi LIKE ? OR fasilitas LIKE ?)
             AND status_ketersediaan = 'Tersedia' ORDER BY nama_studio"
        );
        $kw = "%$keyword%";
        $stmt->execute([$kw, $kw, $kw]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Buat studio baru dengan validasi
    public function createStudio($data) {
        $errors = $this->validate($data, [
            'nama_studio' => 'required',
            'harga_per_jam' => 'required|numeric',
            'kapasitas' => 'numeric'
        ]);
        
        if ($errors) return $this->response(false, $errors);
        
        $data['status_ketersediaan'] = $data['status_ketersediaan'] ?? 'Tersedia';
        
        return $this->insert($data)
            ? $this->response(true, 'Studio berhasil ditambahkan')
            : $this->response(false, 'Gagal menambahkan studio');
    }
    
    // Update data studio
    public function updateStudio($id_studio, $data) {
        $errors = $this->validate($data, [
            'nama_studio' => 'required',
            'harga_per_jam' => 'required|numeric',
            'kapasitas' => 'numeric'
        ]);
        
        if ($errors) return $this->response(false, $errors);
        
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET nama_studio = ?, deskripsi = ?, harga_per_jam = ?, fasilitas = ?, 
                 kapasitas = ?, gambar = ?, status_ketersediaan = ?
             WHERE id_studio = ?"
        );
        
        return $stmt->execute([
            $data['nama_studio'], $data['deskripsi'], $data['harga_per_jam'], $data['fasilitas'],
            $data['kapasitas'], $data['gambar'], $data['status_ketersediaan'], $id_studio
        ]) ? $this->response(true, 'Studio berhasil diupdate') 
           : $this->response(false, 'Gagal mengupdate studio');
    }
    
    // Hapus studio berdasarkan ID
    public function deleteStudio($id_studio) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id_studio = ?");
        return $stmt->execute([$id_studio]);
    }
    
    // Cari studio berdasarkan ID
    public function findById($id_studio) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_studio = ? LIMIT 1");
        $stmt->execute([$id_studio]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
