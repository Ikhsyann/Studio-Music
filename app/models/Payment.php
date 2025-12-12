<?php

require_once __DIR__ . '/../../core/Model.php';

// Model Payment - Mengelola data pembayaran
class Payment extends Model {
    protected $table = 'payments';
    protected $primaryKey = 'id_payment';
    
    // Format response untuk return value
    private function response($success, $messageOrErrors) {
        return array_merge(['success' => $success], 
            is_array($messageOrErrors) ? ['errors' => $messageOrErrors] : ['message' => $messageOrErrors]
        );
    }
    
    // Buat pembayaran baru dengan validasi
    public function createPayment($data) {
        $errors = $this->validate($data, [
            'id_booking' => 'required|numeric',
            'jumlah_bayar' => 'required|numeric',
            'metode_pembayaran' => 'required'
        ]);
        
        if ($errors) return $this->response(false, $errors);
        
        return $this->insert($data) 
            ? $this->response(true, 'Pembayaran berhasil dibuat')
            : $this->response(false, 'Gagal membuat pembayaran');
    }
    
    // Ambil semua pembayaran untuk booking tertentu
    public function getByBooking($id_booking) {
        $stmt = $this->db->prepare(
            "SELECT p.*, b.total_bayar FROM {$this->table} p
             JOIN booking b ON p.id_booking = b.id_booking
             WHERE p.id_booking = ? ORDER BY p.tanggal_pembayaran DESC"
        );
        $stmt->execute([$id_booking]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ambil semua pembayaran dengan detail booking
    public function getAllPayments() {
        $stmt = $this->db->prepare(
            "SELECT p.*, b.id_user, b.id_studio, b.total_bayar, u.nama as nama_user, s.nama_studio
             FROM {$this->table} p
             JOIN booking b ON p.id_booking = b.id_booking
             JOIN users u ON b.id_user = u.id_user
             JOIN studios s ON b.id_studio = s.id_studio
             ORDER BY p.tanggal_pembayaran DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Upload bukti pembayaran
    public function uploadBukti($id_payment, $bukti_pembayaran) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET bukti_pembayaran = ? WHERE id_payment = ?");
        return $stmt->execute([$bukti_pembayaran, $id_payment])
            ? $this->response(true, 'Bukti pembayaran berhasil diupload')
            : $this->response(false, 'Gagal mengupload bukti pembayaran');
    }
}
?>
