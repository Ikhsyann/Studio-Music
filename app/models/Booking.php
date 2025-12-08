<?php

require_once __DIR__ . '/../../core/Model.php';

class Booking extends Model {
    protected $table = 'booking';
    protected $primaryKey = 'id_booking';
    
    public function createBooking($data) {
        $rules = [
            'id_user' => 'required|numeric',
            'id_studio' => 'required|numeric',
            'tanggal_main' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'total_bayar' => 'required|numeric'
        ];
        
        $errors = $this->validate($data, $rules);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        if ($this->isStudioBooked($data['id_studio'], $data['tanggal_main'], $data['jam_mulai'], $data['jam_selesai'])) {
            return ['success' => false, 'message' => 'Studio sudah dibooking di waktu tersebut'];
        }
        
        if (!isset($data['status_booking'])) {
            $data['status_booking'] = 'Menunggu Konfirmasi';
        }
        
        // Do not set `id_admin` here. New bookings should have NULL id_admin until
        // an admin approves/rejects them. The DB migration will allow NULLs.
        
        if ($this->insert($data)) {
            $id = $this->db->lastInsertId();
            return ['success' => true, 'message' => 'Booking berhasil dibuat', 'id_booking' => $id];
        }
        
        return ['success' => false, 'message' => 'Gagal membuat booking'];
    }
    
    public function isStudioBooked($id_studio, $tanggal, $jam_mulai, $jam_selesai) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE id_studio = :id_studio 
                  AND tanggal_main = :tanggal 
                  AND status_booking NOT IN ('Dibatalkan')
                  AND (
                      (jam_mulai < :jam_selesai AND jam_selesai > :jam_mulai)
                  )";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_studio', $id_studio, PDO::PARAM_INT);
        $stmt->bindParam(':tanggal', $tanggal);
        $stmt->bindParam(':jam_mulai', $jam_mulai);
        $stmt->bindParam(':jam_selesai', $jam_selesai);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    public function getBookedHours($id_studio, $tanggal) {
        $query = "SELECT jam_mulai, jam_selesai FROM " . $this->table . " 
                  WHERE id_studio = :id_studio 
                  AND tanggal_main = :tanggal 
                  AND status_booking NOT IN ('Dibatalkan')";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_studio', $id_studio, PDO::PARAM_INT);
        $stmt->bindParam(':tanggal', $tanggal);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $query = "SELECT b.*, s.nama_studio, u.nama as nama_user, u.email as email_user,
                  a.email as admin_email
                  FROM " . $this->table . " b
                  JOIN studios s ON b.id_studio = s.id_studio
                  JOIN users u ON b.id_user = u.id_user
                  LEFT JOIN admin a ON b.id_admin = a.id_admin
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByDate($tanggal) {
        $query = "SELECT b.*, s.nama_studio, u.nama as nama_user, u.email as email_user,
                  a.email as admin_email
                  FROM " . $this->table . " b
                  JOIN studios s ON b.id_studio = s.id_studio
                  JOIN users u ON b.id_user = u.id_user
                  LEFT JOIN admin a ON b.id_admin = a.id_admin
                  WHERE b.tanggal_main = :tanggal
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tanggal', $tanggal);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id_booking, $status, $id_admin = null) {
        if ($id_admin !== null) {
            $query = "UPDATE " . $this->table . " 
                      SET status_booking = :status,
                          id_admin = :id_admin
                      WHERE id_booking = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id_booking, PDO::PARAM_INT);
        } else {
            $query = "UPDATE " . $this->table . " 
                      SET status_booking = :status 
                      WHERE id_booking = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id_booking, PDO::PARAM_INT);
        }
        
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
        
        if ($this->updateStatus($id_booking, 'Dibatalkan')) {
            return ['success' => true, 'message' => 'Booking berhasil dibatalkan'];
        }
        
        return ['success' => false, 'message' => 'Gagal membatalkan booking'];
    }
}
?>
