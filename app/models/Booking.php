<?php

require_once __DIR__ . '/../../core/Model.php';

// Model Booking - Mengelola data pemesanan studio
class Booking extends Model {
    protected $table = 'booking';
    protected $primaryKey = 'id_booking';
    
    // Format response untuk return value
    private function response($success, $messageOrErrors, $extra = []) {
        return array_merge(['success' => $success], 
            is_array($messageOrErrors) ? ['errors' => $messageOrErrors] : ['message' => $messageOrErrors],
            $extra
        );
    }
    
    // Eksekusi query dengan prepared statement
    private function executeQuery($query, $params = []) {
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt;
    }
    
    // Buat booking baru dengan validasi
    public function createBooking($data) {
        $errors = $this->validate($data, [
            'id_user' => 'required|numeric',
            'id_studio' => 'required|numeric',
            'tanggal_main' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'total_bayar' => 'required|numeric'
        ]);
        
        if ($errors) return $this->response(false, $errors);
        
        // Cek apakah studio sudah dibooking
        if ($this->isStudioBooked($data['id_studio'], $data['tanggal_main'], $data['jam_mulai'], $data['jam_selesai'])) {
            return $this->response(false, 'Studio sudah dibooking di waktu tersebut');
        }
        
        $data['status_booking'] = $data['status_booking'] ?? 'Menunggu Konfirmasi';
        
        return $this->insert($data) 
            ? $this->response(true, 'Booking berhasil dibuat', ['id_booking' => $this->db->lastInsertId()])
            : $this->response(false, 'Gagal membuat booking');
    }
    
    // Cek apakah studio sudah dibooking pada waktu tersebut
    public function isStudioBooked($id_studio, $tanggal, $jam_mulai, $jam_selesai) {
        $stmt = $this->executeQuery(
            "SELECT 1 FROM {$this->table} 
             WHERE id_studio = :id_studio AND tanggal_main = :tanggal 
             AND status_booking != 'Dibatalkan'
             AND jam_mulai < :jam_selesai AND jam_selesai > :jam_mulai",
            [':id_studio' => $id_studio, ':tanggal' => $tanggal, ':jam_mulai' => $jam_mulai, ':jam_selesai' => $jam_selesai]
        );
        return $stmt->rowCount() > 0;
    }
    
    // Ambil daftar jam yang sudah dibooking untuk studio tertentu
    public function getBookedHours($id_studio, $tanggal) {
        return $this->executeQuery(
            "SELECT jam_mulai, jam_selesai FROM {$this->table} 
             WHERE id_studio = :id_studio AND tanggal_main = :tanggal AND status_booking != 'Dibatalkan'",
            [':id_studio' => $id_studio, ':tanggal' => $tanggal]
        )->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ambil semua booking milik user tertentu
    public function getByUser($id_user) {
        return $this->executeQuery(
            "SELECT b.*, s.nama_studio, s.harga_per_jam, s.fasilitas, b.id_admin
             FROM {$this->table} b JOIN studios s ON b.id_studio = s.id_studio
             WHERE b.id_user = :id_user ORDER BY b.created_at DESC",
            [':id_user' => $id_user]
        )->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Template query untuk join booking dengan studio, user, dan admin
    private function getBookingsQuery($where = '') {
        return "SELECT b.*, s.nama_studio, u.nama as nama_user, u.email as email_user, a.email as admin_email
                FROM {$this->table} b
                JOIN studios s ON b.id_studio = s.id_studio
                JOIN users u ON b.id_user = u.id_user
                LEFT JOIN admin a ON b.id_admin = a.id_admin
                $where ORDER BY b.created_at DESC";
    }
    
    // Ambil semua booking dengan data lengkap
    public function getAllBookings() {
        return $this->executeQuery($this->getBookingsQuery())->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ambil booking berdasarkan tanggal
    public function getByDate($tanggal) {
        return $this->executeQuery(
            $this->getBookingsQuery('WHERE b.tanggal_main = :tanggal'),
            [':tanggal' => $tanggal]
        )->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Update status booking (dengan admin id opsional)
    public function updateStatus($id_booking, $status, $id_admin = null) {
        $params = [':status' => $status, ':id' => $id_booking];
        $set = 'status_booking = :status' . ($id_admin ? ', id_admin = :id_admin' : '');
        if ($id_admin) $params[':id_admin'] = $id_admin;
        
        return $this->executeQuery("UPDATE {$this->table} SET $set WHERE id_booking = :id", $params)->rowCount() > 0;
    }
    
    // Ambil detail booking berdasarkan ID
    public function getById($id_booking) {
        return $this->executeQuery(
            "SELECT b.*, s.nama_studio, s.harga_per_jam, u.nama as nama_user
             FROM {$this->table} b
             JOIN studios s ON b.id_studio = s.id_studio
             JOIN users u ON b.id_user = u.id_user
             WHERE b.id_booking = :id LIMIT 1",
            [':id' => $id_booking]
        )->fetch(PDO::FETCH_ASSOC);
    }
    
    // Batalkan booking oleh user
    public function cancelBooking($id_booking, $id_user) {
        $stmt = $this->executeQuery(
            "SELECT 1 FROM {$this->table} WHERE id_booking = :id AND id_user = :id_user",
            [':id' => $id_booking, ':id_user' => $id_user]
        );
        
        if ($stmt->rowCount() == 0) return $this->response(false, 'Booking tidak ditemukan');
        
        return $this->updateStatus($id_booking, 'Dibatalkan')
            ? $this->response(true, 'Booking berhasil dibatalkan, Silahkan hubungi admin untuk pengembalian dana. Wa. 085705012504')
            : $this->response(false, 'Gagal membatalkan booking');
    }
}
?>
