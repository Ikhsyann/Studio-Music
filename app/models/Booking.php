<?php

require_once __DIR__ . '/../../core/Model.php';

class Booking extends Model {
    protected $table = 'bookings';
    protected $primaryKey = 'id_booking';
    
    public function createBooking($data) {
        $rules = [
            'id_user' => 'required|numeric',
            'id_studio' => 'required|numeric',
            'tanggal_booking' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required'
        ];
        
        $errors = $this->validate($data, $rules);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        if ($this->isStudioBooked($data['id_studio'], $data['tanggal_booking'], $data['waktu_mulai'], $data['waktu_selesai'])) {
            return ['success' => false, 'message' => 'Studio sudah dibooking di waktu tersebut'];
        }
        
        if (!isset($data['status_booking'])) {
            $data['status_booking'] = 'pending';
        }
        
        if ($this->insert($data)) {
            return ['success' => true, 'message' => 'Booking berhasil dibuat'];
        }
        
        return ['success' => false, 'message' => 'Gagal membuat booking'];
    }
    
    public function isStudioBooked($id_studio, $tanggal, $waktu_mulai, $waktu_selesai) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE id_studio = :id_studio 
                  AND tanggal_booking = :tanggal 
                  AND status_booking NOT IN ('dibatalkan')
                  AND (
                      (waktu_mulai < :waktu_selesai AND waktu_selesai > :waktu_mulai)
                  )";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_studio', $id_studio, PDO::PARAM_INT);
        $stmt->bindParam(':tanggal', $tanggal);
        $stmt->bindParam(':waktu_mulai', $waktu_mulai);
        $stmt->bindParam(':waktu_selesai', $waktu_selesai);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    public function getByUser($id_user) {
        $query = "SELECT b.*, s.nama_studio, s.harga_per_jam, s.fasilitas
                  FROM " . $this->table . " b
                  JOIN studios s ON b.id_studio = s.id_studio
                  WHERE b.id_user = :id_user
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllBookings() {
        $query = "SELECT b.*, s.nama_studio, u.nama as nama_user, u.email as email_user
                  FROM " . $this->table . " b
                  JOIN studios s ON b.id_studio = s.id_studio
                  JOIN users u ON b.id_user = u.id_user
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id_booking, $status) {
        $query = "UPDATE " . $this->table . " 
                  SET status_booking = :status 
                  WHERE id_booking = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id_booking, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function getById($id_booking) {
        $query = "SELECT b.*, s.nama_studio, s.harga_per_jam, u.nama as nama_user
                  FROM " . $this->table . " b
                  JOIN studios s ON b.id_studio = s.id_studio
                  JOIN users u ON b.id_user = u.id_user
                  WHERE b.id_booking = :id
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id_booking, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function cancelBooking($id_booking, $id_user) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_booking = :id AND id_user = :id_user LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id_booking, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            return ['success' => false, 'message' => 'Booking tidak ditemukan'];
        }
        
        if ($this->updateStatus($id_booking, 'dibatalkan')) {
            return ['success' => true, 'message' => 'Booking berhasil dibatalkan'];
        }
        
        return ['success' => false, 'message' => 'Gagal membatalkan booking'];
    }
}
?>
