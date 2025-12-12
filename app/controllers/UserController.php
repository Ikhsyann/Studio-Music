<?php

require_once __DIR__ . '/../../core/Controller.php';

// Controller untuk halaman user (booking, payment, riwayat)
class UserController extends Controller {
    
    public function __construct() {
        $this->ensureSession();
        // Redirect jika belum login
        if (!isset($_SESSION['user'])) $this->redirect('/Studio-Music/public/index.php?url=auth/login');
    }
    
    // Tampilkan dashboard user dengan daftar studio
    public function dashboard() {
        $this->view('user/dashboard', [
            'user' => $_SESSION['user'],
            'studios' => $this->model('Studio')->getAvailable(),
            'title' => 'Dashboard - Studio Musik'
        ]);
    }
    
    // Tampilkan riwayat booking user
    public function riwayat() {
        $paymentModel = $this->model('Payment');
        $bookings = $this->model('Booking')->getByUser($_SESSION['user']['id_user']);
        
        // Ambil data payment untuk setiap booking
        foreach ($bookings as &$booking) {
            $booking['payment'] = $paymentModel->getByBooking($booking['id_booking'])[0] ?? null;
        }
        
        $this->view('user/riwayat', ['user' => $_SESSION['user'], 'bookings' => $bookings, 'title' => 'Riwayat Booking']);
    }
    
    // Tampilkan status booking (semua user atau filter by tanggal)
    public function statusBooking() {
        $bookingModel = $this->model('Booking');
        $tanggal = $_GET['tanggal'] ?? null;
        
        $this->view('user/status_booking', [
            'user' => $_SESSION['user'],
            'bookings' => $tanggal ? $bookingModel->getByDate($tanggal) : $bookingModel->getAllBookings(),
            'title' => 'Status Booking - Jadwal Studio'
        ]);
    }
    
    // Tampilkan form booking untuk studio tertentu
    public function booking($id_studio = null) {
        if (!$id_studio || !($studio = $this->model('Studio')->findById($id_studio))) {
            $this->setFlash('error', 'Studio tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
        }
        
        $this->view('user/booking_form', [
            'user' => $_SESSION['user'],
            'studio' => $studio,
            'title' => 'Form Booking - ' . $studio['nama_studio']
        ]);
    }
    
    // Proses booking: validasi input dan simpan ke session sementara
    public function bookingProcess() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_studio = $_POST['id_studio'] ?? '';
            $selected_hours = json_decode($_POST['selected_hours'] ?? '[]', true);
            
            if (empty($selected_hours)) {
                $this->setFlash('error', 'Silakan pilih minimal 1 jam untuk booking');
                $this->redirect('/Studio-Music/public/index.php?url=user/booking/' . $id_studio);
                return;
            }
            
            if (!($studio = $this->model('Studio')->findById($id_studio))) {
                $this->setFlash('error', 'Studio tidak ditemukan');
                $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
                return;
            }
            
            sort($selected_hours);
            $durasi = count($selected_hours);
            
            $_SESSION['booking_temp'] = [
                'id_user' => $_SESSION['user']['id_user'],
                'id_studio' => $id_studio,
                'tanggal_main' => $_POST['tanggal_main'] ?? '',
                'jam_mulai' => sprintf('%02d:00:00', $selected_hours[0]),
                'jam_selesai' => sprintf('%02d:00:00', end($selected_hours) + 1),
                'total_bayar' => $durasi * $studio['harga_per_jam'],
                'nama_studio' => $studio['nama_studio'],
                'durasi' => $durasi
            ];
            
            $this->redirect('/Studio-Music/public/index.php?url=user/payment');
        }
    }
    
    // AJAX: Ambil jam yang sudah dibooking untuk studio tertentu
    public function getBookedHours() {
        header('Content-Type: application/json');
        $id_studio = $_GET['id_studio'] ?? 0;
        $tanggal = $_GET['tanggal'] ?? '';
        
        echo json_encode(($id_studio && $tanggal) ? $this->model('Booking')->getBookedHours($id_studio, $tanggal) : []);
        exit;
    }
    
    // Tampilkan form pembayaran (dari session sementara)
    public function payment() {
        if (!isset($_SESSION['booking_temp'])) $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
        
        $this->view('user/payment_form', [
            'user' => $_SESSION['user'],
            'booking' => $_SESSION['booking_temp'],
            'title' => 'Form Pembayaran'
        ]);
    }
    
    // Proses pembayaran: buat booking dan upload bukti bayar
    public function paymentProcess() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['booking_temp'])) {
                $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
                return;
            }

            $bookingTemp = $_SESSION['booking_temp'];
            
            if (!isset($_FILES['bukti_pembayaran'])) {
                $this->setFlash('error', 'Form tidak mengirim file. Silakan coba lagi.');
                $this->redirect('/Studio-Music/public/index.php?url=user/payment');
                return;
            }

            $fileError = $_FILES['bukti_pembayaran']['error'];
            if ($fileError !== UPLOAD_ERR_OK) {
                $errors = [UPLOAD_ERR_INI_SIZE => 'File melebihi batas upload server.', UPLOAD_ERR_FORM_SIZE => 'File melebihi batas ukuran form.',
                           UPLOAD_ERR_PARTIAL => 'File hanya ter-upload sebagian.', UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload.',
                           UPLOAD_ERR_NO_TMP_DIR => 'Folder tmp server hilang.', UPLOAD_ERR_CANT_WRITE => 'Server gagal menulis file ke disk.',
                           UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP.'];
                $this->setFlash('error', 'Gagal upload file: ' . ($errors[$fileError] ?? 'Error tidak diketahui'));
                $this->redirect('/Studio-Music/public/index.php?url=user/payment');
                return;
            }

            $upload_dir = __DIR__ . '/../../public/images/payments/';
            if (!is_dir($upload_dir)) @mkdir($upload_dir, 0775, true);
            if (is_dir($upload_dir) && !is_writable($upload_dir)) @chmod($upload_dir, 0775) || @chmod($upload_dir, 0777);
            
            if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
                $perms = is_dir($upload_dir) ? substr(sprintf('%o', fileperms($upload_dir)), -4) : 'N/A';
                $this->setFlash('error', 'Folder upload tidak bisa ditulisi (perms: ' . $perms . '). Jalankan: sudo mkdir -p ' . $upload_dir . ' && sudo chown -R apache:apache ' . $upload_dir . ' && sudo chmod -R 775 ' . $upload_dir . ' (Jika SELinux aktif: sudo chcon -R -t httpd_sys_rw_content_t ' . $upload_dir . ')');
                $this->redirect('/Studio-Music/public/index.php?url=user/payment');
                return;
            }

            $file_extension = strtolower(pathinfo($_FILES['bukti_pembayaran']['name'], PATHINFO_EXTENSION));
            if (!in_array($file_extension, ['jpg','jpeg','png','pdf'])) {
                $this->setFlash('error', 'Format file tidak valid. Hanya JPG, JPEG, PNG, atau PDF');
                $this->redirect('/Studio-Music/public/index.php?url=user/payment');
                return;
            }
            if ($_FILES['bukti_pembayaran']['size'] > 10485760) {
                $this->setFlash('error', 'Ukuran file terlalu besar (max 10MB)');
                $this->redirect('/Studio-Music/public/index.php?url=user/payment');
                return;
            }

            $tempFilename = 'temp_' . $_SESSION['user']['id_user'] . '_' . time() . '.' . $file_extension;
            $tempPath = $upload_dir . $tempFilename;
            if (!move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $tempPath)) {
                $this->setFlash('error', 'Gagal memindahkan file ke server. Silakan coba lagi.');
                $this->redirect('/Studio-Music/public/index.php?url=user/payment');
                return;
            }

            $bookingModel = $this->model('Booking');
            $bookingResult = $bookingModel->createBooking([
                'id_user' => $bookingTemp['id_user'], 'id_studio' => $bookingTemp['id_studio'],
                'tanggal_main' => $bookingTemp['tanggal_main'], 'jam_mulai' => $bookingTemp['jam_mulai'],
                'jam_selesai' => $bookingTemp['jam_selesai'], 'total_bayar' => $bookingTemp['total_bayar'],
                'status_booking' => 'Menunggu Konfirmasi'
            ]);
            
            if (!$bookingResult['success'] || !($id_booking = $bookingResult['id_booking'] ?? null)) {
                if (file_exists($tempPath)) unlink($tempPath);
                unset($_SESSION['booking_temp']);
                $this->setFlash('error', $bookingResult['success'] ? 'Tidak dapat menentukan ID booking. Silakan coba lagi.' : 'Gagal membuat booking: ' . $bookingResult['message']);
                $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
                return;
            }

            $finalFilename = 'payment_' . $id_booking . '_' . time() . '.' . $file_extension;
            $finalPath = $upload_dir . $finalFilename;
            if (!rename($tempPath, $finalPath)) {
                $finalFilename = $tempFilename;
                $finalPath = $tempPath;
            }

            $paymentResult = $this->model('Payment')->createPayment([
                'id_booking' => $id_booking,
                'jumlah_bayar' => $_POST['jumlah_bayar'] ?? $bookingTemp['total_bayar'],
                'metode_pembayaran' => $_POST['metode_pembayaran'] ?? 'Transfer Bank',
                'bukti_pembayaran' => $finalFilename,
                'keterangan' => $_POST['keterangan'] ?? ''
            ]);
            
            if (!$paymentResult['success']) {
                $bookingModel->delete($id_booking);
                if (file_exists($finalPath)) unlink($finalPath);
                unset($_SESSION['booking_temp']);
                $this->setFlash('error', 'Gagal menyimpan data pembayaran. Booking dibatalkan.');
                $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
                return;
            }

            unset($_SESSION['booking_temp']);
            $this->setFlash('success', 'Bukti pembayaran berhasil diupload! Menunggu persetujuan admin.');
            $this->redirect('/Studio-Music/public/index.php?url=user/riwayat');
        }
    }
    
    // Batalkan booking oleh user
    public function cancelBooking() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->model('Booking')->cancelBooking($_POST['id_booking'] ?? 0, $_SESSION['user']['id_user']);
            $this->setFlash($result['success'] ? 'success' : 'error', $result['message']);
            $this->redirect('/Studio-Music/public/index.php?url=user/riwayat');
        }
    }
}
?>
