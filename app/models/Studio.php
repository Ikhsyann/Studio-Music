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
    
    // Ambil studio yang tersedia - MENGGUNAKAN QUERY BUILDER
    public function getAvailable() {
        return $this->query()
                    ->table($this->table)
                    ->where('status_ketersediaan', 'Tersedia')
                    ->orderBy('nama_studio', 'ASC')
                    ->get();
    }
    
    // Cari studio berdasarkan keyword
    // Note: Query Builder belum support multiple OR LIKE conditions,
    // jadi masih menggunakan raw SQL untuk kondisi pencarian yang kompleks
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
    
    // Update data studio - MENGGUNAKAN QUERY BUILDER
    public function updateStudio($id_studio, $data) {
        $errors = $this->validate($data, [
            'nama_studio' => 'required',
            'harga_per_jam' => 'required|numeric',
            'kapasitas' => 'numeric'
        ]);
        
        if ($errors) return $this->response(false, $errors);
        
        $affected = $this->query()
                         ->table($this->table)
                         ->where('id_studio', $id_studio)
                         ->update([
                             'nama_studio' => $data['nama_studio'],
                             'deskripsi' => $data['deskripsi'],
                             'harga_per_jam' => $data['harga_per_jam'],
                             'fasilitas' => $data['fasilitas'],
                             'kapasitas' => $data['kapasitas'],
                             'gambar' => $data['gambar'],
                             'status_ketersediaan' => $data['status_ketersediaan']
                         ]);
        
        return $affected > 0
            ? $this->response(true, 'Studio berhasil diupdate') 
            : $this->response(false, 'Gagal mengupdate studio');
    }
    
    // Hapus studio berdasarkan ID - MENGGUNAKAN QUERY BUILDER
    public function deleteStudio($id_studio) {
        return $this->query()
                    ->table($this->table)
                    ->where('id_studio', $id_studio)
                    ->delete() > 0;
    }
    
    // Cari studio berdasarkan ID - MENGGUNAKAN QUERY BUILDER
    public function findById($id_studio) {
        return $this->query()
                    ->table($this->table)
                    ->where('id_studio', $id_studio)
                    ->first();
    }
}
?>
