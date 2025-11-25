<?php

require_once __DIR__ . '/../../core/Controller.php';

class UserController extends Controller {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user'])) {
            header('Location: /Studio-Music/public/index.php?url=auth/login');
            exit;
        }
    }
    
    public function dashboard() {
        $studioModel = $this->model('Studio');
        $studios = $studioModel->getAvailable();
        
        $data = [
            'user' => $_SESSION['user'],
            'studios' => $studios,
            'title' => 'Dashboard - Studio Musik'
        ];
        
        $this->view('user/dashboard', $data);
    }
    
    public function riwayat() {
        $bookingModel = $this->model('Booking');
        $paymentModel = $this->model('Payment');
        
        $bookings = $bookingModel->getByUser($_SESSION['user']['id_user']);
        
        foreach ($bookings as &$booking) {
            $payments = $paymentModel->getByBooking($booking['id_booking']);
            $booking['payment'] = $payments[0] ?? null;
        }
        
        $data = [
            'user' => $_SESSION['user'],
            'bookings' => $bookings,
            'title' => 'Riwayat Booking'
        ];
        
        $this->view('user/riwayat', $data);
    }
    
    public function statusBooking() {
        $bookingModel = $this->model('Booking');
        
        $tanggal = $_GET['tanggal'] ?? null;
        
        if ($tanggal) {
            $allBookings = $bookingModel->getByDate($tanggal);
        } else {
            $allBookings = $bookingModel->getAllBookings();
        }
        
        $data = [
            'user' => $_SESSION['user'],
            'bookings' => $allBookings,
            'title' => 'Status Booking - Jadwal Studio'
        ];
        
        $this->view('user/status_booking', $data);
    }
    
    public function booking($id_studio = null) {
        if (!$id_studio) {
            $this->setFlash('error', 'Studio tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
        }
        
        $studioModel = $this->model('Studio');
        $studio = $studioModel->findById($id_studio);
        
        if (!$studio) {
            $this->setFlash('error', 'Studio tidak ditemukan');
            $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
        }
        
        $data = [
            'user' => $_SESSION['user'],
            'studio' => $studio,
            'title' => 'Form Booking - ' . $studio['nama_studio']
        ];
        
        $this->view('user/booking_form', $data);
    }
    
    public function bookingProcess() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_studio = $_POST['id_studio'] ?? '';
            $tanggal_main = $_POST['tanggal_main'] ?? '';
            $selected_hours = json_decode($_POST['selected_hours'] ?? '[]', true);
            
            if (empty($selected_hours)) {
                $this->setFlash('error', 'Silakan pilih minimal 1 jam untuk booking');
                $this->redirect('/Studio-Music/public/index.php?url=user/booking/' . $id_studio);
                return;
            }
            
            sort($selected_hours);
            $jam_mulai = $selected_hours[0];
            $jam_selesai = end($selected_hours) + 1;
            

            $jam_mulai_str = sprintf('%02d:00:00', $jam_mulai);
            $jam_selesai_str = sprintf('%02d:00:00', $jam_selesai);
            
            $studioModel = $this->model('Studio');
            $studio = $studioModel->findById($id_studio);
            
            if (!$studio) {
                $this->setFlash('error', 'Studio tidak ditemukan');
                $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
                return;
            }
            
            $durasi = count($selected_hours);
            $total_bayar = $durasi * $studio['harga_per_jam'];
            
            $_SESSION['booking_temp'] = [
                'id_user' => $_SESSION['user']['id_user'],
                'id_studio' => $id_studio,
                'tanggal_main' => $tanggal_main,
                'jam_mulai' => $jam_mulai_str,
                'jam_selesai' => $jam_selesai_str,
                'total_bayar' => $total_bayar,
                'nama_studio' => $studio['nama_studio'],
                'durasi' => $durasi
            ];
            
            $this->redirect('/Studio-Music/public/index.php?url=user/payment');
        }
    }
    
    public function getBookedHours() {
        header('Content-Type: application/json');
        
        $id_studio = $_GET['id_studio'] ?? 0;
        $tanggal = $_GET['tanggal'] ?? '';
        
        if (!$id_studio || !$tanggal) {
            echo json_encode([]);
            return;
        }
        
        $bookingModel = $this->model('Booking');
        $bookedHours = $bookingModel->getBookedHours($id_studio, $tanggal);
        
        echo json_encode($bookedHours);
        exit;
    }
    
    public function payment() {
        if (!isset($_SESSION['booking_temp'])) {
            $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
        }
        
        $data = [
            'user' => $_SESSION['user'],
            'booking' => $_SESSION['booking_temp'],
            'title' => 'Form Pembayaran'
        ];
        
        $this->view('user/payment_form', $data);
    }
    
    public function paymentProcess() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['booking_temp'])) {
                $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
                return;
            }

            $bookingTemp = $_SESSION['booking_temp'];
            $jumlah_bayar = $_POST['jumlah_bayar'] ?? $bookingTemp['total_bayar'];
            $metode_pembayaran = $_POST['metode_pembayaran'] ?? 'Transfer Bank';
            $keterangan = $_POST['keterangan'] ?? '';

            if (!isset($_FILES['bukti_pembayaran'])) {
                $this->setFlash('error', 'Form tidak mengirim file. Silakan coba lagi.');
                $this->redirect('/Studio-Music/public/index.php?url=user/payment');
                return;
            }

            $fileError = $_FILES['bukti_pembayaran']['error'];
            if ($fileError !== UPLOAD_ERR_OK) {
                $messages = [
                    UPLOAD_ERR_INI_SIZE => 'File melebihi batas upload server.',
                    UPLOAD_ERR_FORM_SIZE => 'File melebihi batas ukuran form.',
                    UPLOAD_ERR_PARTIAL => 'File hanya ter-upload sebagian.',
                    UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Folder tmp server hilang.',
                    UPLOAD_ERR_CANT_WRITE => 'Server gagal menulis file ke disk.',
                    UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP.'
                ];
                $this->setFlash('error', 'Gagal upload file: ' . ($messages[$fileError] ?? 'Error tidak diketahui') );
                $this->redirect('/Studio-Music/public/index.php?url=user/payment');
                return;
            }

            $upload_dir = __DIR__ . '/../../public/images/payments/';
            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0775, true);
            }
            if (is_dir($upload_dir) && !is_writable($upload_dir)) {
                @chmod($upload_dir, 0775);
            }
            if (is_dir($upload_dir) && !is_writable($upload_dir)) {
                @chmod($upload_dir, 0777);
            }
            if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
                $perms = is_dir($upload_dir) ? substr(sprintf('%o', fileperms($upload_dir)), -4) : 'N/A';
                $hint = 'sudo mkdir -p ' . $upload_dir . ' && sudo chown -R apache:apache ' . $upload_dir . ' && sudo chmod -R 775 ' . $upload_dir;
                $selinuxHint = ' (Jika SELinux aktif: sudo chcon -R -t httpd_sys_rw_content_t ' . $upload_dir . ')';
                $this->setFlash('error', 'Folder upload tidak bisa ditulisi (perms: ' . $perms . '). Jalankan: ' . $hint . $selinuxHint);
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

            $bookingData = [
                'id_user' => $bookingTemp['id_user'],
                'id_studio' => $bookingTemp['id_studio'],
                'tanggal_main' => $bookingTemp['tanggal_main'],
                'jam_mulai' => $bookingTemp['jam_mulai'],
                'jam_selesai' => $bookingTemp['jam_selesai'],
                'total_bayar' => $bookingTemp['total_bayar'],
                'status_booking' => 'Menunggu Konfirmasi'
            ];
            $bookingModel = $this->model('Booking');
            $bookingResult = $bookingModel->createBooking($bookingData);
            if (!$bookingResult['success']) {
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
                unset($_SESSION['booking_temp']);
                $this->setFlash('error', 'Gagal membuat booking: ' . $bookingResult['message']);
                $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
                return;
            }

            $id_booking = $bookingResult['id_booking'] ?? null;
            if (!$id_booking) {
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
                unset($_SESSION['booking_temp']);
                $this->setFlash('error', 'Tidak dapat menentukan ID booking. Silakan coba lagi.');
                $this->redirect('/Studio-Music/public/index.php?url=user/dashboard');
                return;
            }

            $finalFilename = 'payment_' . $id_booking . '_' . time() . '.' . $file_extension;
            $finalPath = $upload_dir . $finalFilename;
            if (!rename($tempPath, $finalPath)) {
                $finalFilename = $tempFilename;
                $finalPath = $tempPath;
            }

            $paymentData = [
                'id_booking' => $id_booking,
                'jumlah_bayar' => $jumlah_bayar,
                'metode_pembayaran' => $metode_pembayaran,
                'bukti_pembayaran' => $finalFilename,
                'keterangan' => $keterangan
            ];
            $paymentModel = $this->model('Payment');
            $paymentResult = $paymentModel->createPayment($paymentData);
            if (!$paymentResult['success']) {
                $bookingModel->delete($id_booking);
                if (file_exists($finalPath)) {
                    unlink($finalPath);
                }
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
    
    public function cancelBooking() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_booking = $_POST['id_booking'] ?? 0;
            
            $bookingModel = $this->model('Booking');
            $result = $bookingModel->cancelBooking($id_booking, $_SESSION['user']['id_user']);
            
            if ($result['success']) {
                $this->setFlash('success', $result['message']);
            } else {
                $this->setFlash('error', $result['message']);
            }
            
            $this->redirect('/Studio-Music/public/index.php?url=user/riwayat');
        }
    }
}
?>
