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
    
    // Ambil semua pembayaran untuk booking tertentu - MENGGUNAKAN QUERY BUILDER
    public function getByBooking($id_booking) {
        return $this->query()
                    ->table("{$this->table} p")
                    ->select('p.*, b.total_bayar')
                    ->join('booking b', 'p.id_booking', '=', 'b.id_booking')
                    ->where('p.id_booking', $id_booking)
                    ->orderBy('p.tanggal_pembayaran', 'DESC')
                    ->get();
    }
    
    // Ambil semua pembayaran dengan detail booking - MENGGUNAKAN QUERY BUILDER
    public function getAllPayments() {
        return $this->query()
                    ->table("{$this->table} p")
                    ->select('p.*, b.id_user, b.id_studio, b.total_bayar, u.nama as nama_user, s.nama_studio')
                    ->join('booking b', 'p.id_booking', '=', 'b.id_booking')
                    ->join('users u', 'b.id_user', '=', 'u.id_user')
                    ->join('studios s', 'b.id_studio', '=', 's.id_studio')
                    ->orderBy('p.tanggal_pembayaran', 'DESC')
                    ->get();
    }
    
    // Upload bukti pembayaran - MENGGUNAKAN QUERY BUILDER
    public function uploadBukti($id_payment, $bukti_pembayaran) {
        $affected = $this->query()
                         ->table($this->table)
                         ->where('id_payment', $id_payment)
                         ->update(['bukti_pembayaran' => $bukti_pembayaran]);
        
        return $affected > 0
            ? $this->response(true, 'Bukti pembayaran berhasil diupload')
            : $this->response(false, 'Gagal mengupload bukti pembayaran');
    }
}
?>
