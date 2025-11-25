<?php

require_once __DIR__ . '/../../core/Model.php';

class Studio extends Model {
    protected $table = 'studios';
    protected $primaryKey = 'id_studio';
    
    public function getAvailable() {
        $query = "SELECT * FROM " . $this->table . " WHERE status_ketersediaan = 'Tersedia' ORDER BY nama_studio ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE (nama_studio LIKE :keyword OR deskripsi LIKE :keyword OR fasilitas LIKE :keyword)
                  AND status_ketersediaan = 'Tersedia'
                  ORDER BY nama_studio ASC";
        $stmt = $this->db->prepare($query);
        $keyword = "%$keyword%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function createStudio($data) {
        $rules = [
            'nama_studio' => 'required',
            'harga_per_jam' => 'required|numeric',
            'kapasitas' => 'numeric'
        ];
        
        $errors = $this->validate($data, $rules);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        if (!isset($data['status_ketersediaan'])) {
            $data['status_ketersediaan'] = 'Tersedia';
        }
        
        if ($this->insert($data)) {
            return ['success' => true, 'message' => 'Studio berhasil ditambahkan'];
        }
        
        return ['success' => false, 'message' => 'Gagal menambahkan studio'];
    }
    
    public function updateStudio($id_studio, $data) {
        $rules = [
            'nama_studio' => 'required',
            'harga_per_jam' => 'required|numeric',
            'kapasitas' => 'numeric'
        ];
        
        $errors = $this->validate($data, $rules);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET nama_studio = :nama_studio, 
                      deskripsi = :deskripsi,
                      harga_per_jam = :harga_per_jam,
                      fasilitas = :fasilitas,
                      kapasitas = :kapasitas,
                      gambar = :gambar,
                      status_ketersediaan = :status_ketersediaan
                  WHERE id_studio = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nama_studio', $data['nama_studio']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        $stmt->bindParam(':harga_per_jam', $data['harga_per_jam']);
        $stmt->bindParam(':fasilitas', $data['fasilitas']);
        $stmt->bindParam(':kapasitas', $data['kapasitas']);
        $stmt->bindParam(':gambar', $data['gambar']);
        $stmt->bindParam(':status_ketersediaan', $data['status_ketersediaan']);
        $stmt->bindParam(':id', $id_studio, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Studio berhasil diupdate'];
        }
        
        return ['success' => false, 'message' => 'Gagal mengupdate studio'];
    }
    
    public function deleteStudio($id_studio) {
        $query = "DELETE FROM " . $this->table . " WHERE id_studio = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id_studio, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function findById($id_studio) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_studio = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id_studio, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
