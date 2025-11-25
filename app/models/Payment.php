<?php

require_once __DIR__ . '/../../core/Model.php';

class Payment extends Model {
    protected $table = 'payments';
    protected $primaryKey = 'id_payment';
    
    public function createPayment($data) {
        $rules = [
            'id_booking' => 'required|numeric',
            'jumlah_bayar' => 'required|numeric',
            'metode_pembayaran' => 'required'
        ];
        
        $errors = $this->validate($data, $rules);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        if ($this->insert($data)) {
            return ['success' => true, 'message' => 'Pembayaran berhasil dibuat'];
        }
        
        return ['success' => false, 'message' => 'Gagal membuat pembayaran'];
    }
    
    public function getByBooking($id_booking) {
        $query = "SELECT p.*, b.total_bayar
                  FROM " . $this->table . " p
                  JOIN booking b ON p.id_booking = b.id_booking
                  WHERE p.id_booking = :id_booking
                  ORDER BY p.tanggal_pembayaran DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_booking', $id_booking, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllPayments() {
        $query = "SELECT p.*, b.id_user, b.id_studio, b.total_bayar,
                  u.nama as nama_user, s.nama_studio
                  FROM " . $this->table . " p
                  JOIN booking b ON p.id_booking = b.id_booking
                  JOIN users u ON b.id_user = u.id_user
                  JOIN studios s ON b.id_studio = s.id_studio
                  ORDER BY p.tanggal_pembayaran DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function uploadBukti($id_payment, $bukti_pembayaran) {
        $query = "UPDATE " . $this->table . " 
                  SET bukti_pembayaran = :bukti_pembayaran
                  WHERE id_payment = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':bukti_pembayaran', $bukti_pembayaran);
        $stmt->bindParam(':id', $id_payment, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Bukti pembayaran berhasil diupload'];
        }
        
        return ['success' => false, 'message' => 'Gagal mengupload bukti pembayaran'];
    }
}
?>
