<?php

require_once __DIR__ . '/../../core/Model.php';

class RiwayatBooking extends Model {
    protected $table = 'riwayat_booking';
    protected $primaryKey = 'id_riwayat';
    
    public function addRiwayat($data) {
        $rules = [
            'id_booking' => 'required|numeric',
            'status' => 'required'
        ];
        
        $errors = $this->validate($data, $rules);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        if ($this->insert($data)) {
            return ['success' => true, 'message' => 'Riwayat berhasil ditambahkan'];
        }
        
        return ['success' => false, 'message' => 'Gagal menambahkan riwayat'];
    }
    
    public function getByBooking($id_booking) {
        $query = "SELECT r.*, a.nama as nama_admin 
                  FROM " . $this->table . " r
                  LEFT JOIN admins a ON r.id_admin = a.id_admin
                  WHERE r.id_booking = :id_booking
                  ORDER BY r.waktu_update DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_booking', $id_booking, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllRiwayat() {
        $query = "SELECT r.*, a.nama as nama_admin, b.id_user, u.nama as nama_user
                  FROM " . $this->table . " r
                  LEFT JOIN admins a ON r.id_admin = a.id_admin
                  JOIN bookings b ON r.id_booking = b.id_booking
                  JOIN users u ON b.id_user = u.id_user
                  ORDER BY r.waktu_update DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function logStatusChange($id_booking, $status, $id_admin = null, $keterangan = '') {
        $data = [
            'id_booking' => $id_booking,
            'status' => $status,
            'keterangan' => $keterangan
        ];
        
        if ($id_admin !== null) {
            $data['id_admin'] = $id_admin;
        }
        
        $result = $this->addRiwayat($data);
        return $result['success'];
    }
    
    public function getLatestByBooking($id_booking) {
        $query = "SELECT r.*, a.nama as nama_admin 
                  FROM " . $this->table . " r
                  LEFT JOIN admins a ON r.id_admin = a.id_admin
                  WHERE r.id_booking = :id_booking
                  ORDER BY r.waktu_update DESC
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_booking', $id_booking, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
