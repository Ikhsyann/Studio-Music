<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="payment-container">
    <div class="payment-header">
        <h1>Form Pembayaran</h1>
        <p>Upload bukti pembayaran untuk menyelesaikan booking</p>
    </div>
    
    <div class="payment-content">
        <div class="booking-summary">
            <h3>Ringkasan Booking</h3>
            <table class="summary-table">
                <tr>
                    <td>Nama Studio</td>
                    <td>: <?= htmlspecialchars($booking['nama_studio']) ?></td>
                </tr>
                <tr>
                    <td>Total Bayar</td>
                    <td><strong>: Rp. <?= number_format($booking['total_bayar'], 0, ',', '.') ?></strong></td>
                </tr>
            </table>
        </div>
        
        <div class="payment-info">
            <h3>Informasi Pembayaran</h3>
            <p>Silakan pilih metode pembayaran dan transfer ke rekening yang sesuai:</p>
        </div>
        
        <form action="/Studio-Music/public/index.php?url=user/paymentProcess" method="POST" enctype="multipart/form-data" class="payment-form">
            <input type="hidden" name="jumlah_bayar" value="<?= $booking['total_bayar'] ?>">
            
            <div class="form-group">
                <label for="metode_pembayaran">Metode Pembayaran <span style="color: red;">*</span></label>
                <select id="metode_pembayaran" name="metode_pembayaran" class="form-control" required onchange="showPaymentInfo(this.value)">
                    <?php $oldMethod = old('metode_pembayaran', ''); ?>
                    <option value="" <?= $oldMethod === '' ? 'selected' : '' ?>>-- Pilih Metode Pembayaran --</option>
                    <option value="Transfer Bank" <?= $oldMethod === 'Transfer Bank' ? 'selected' : '' ?>>Transfer Bank</option>
                    <option value="E-Wallet" <?= $oldMethod === 'E-Wallet' ? 'selected' : '' ?>>E-Wallet</option>
                </select>
            </div>

            <div id="bank-info" class="payment-method-info" style="display: none;">
                <div class="bank-accounts">
                    <div class="bank-account">
                        <strong>Bank BCA</strong><br>
                        No. Rek: 1234567890<br>
                        a.n. Studio Musik
                    </div>
                    <div class="bank-account">
                        <strong>Bank Mandiri</strong><br>
                        No. Rek: 0987654321<br>
                        a.n. Studio Musik
                    </div>
                </div>
            </div>

            <div id="ewallet-info" class="payment-method-info" style="display: none;">
                <div class="bank-accounts">
                    <div class="bank-account">
                        <strong>E-Wallet Dana</strong><br>
                        No. Rek: 085705012504<br>
                        a.n. Studio Musik
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="bukti_pembayaran">Upload Bukti Pembayaran <span style="color: red;">*</span></label>
                <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf" required>
                <small>Format: JPG / JPEG / PNG / PDF (Max 10MB)</small>
            </div>

            <script>
            function showPaymentInfo(method) {
                document.getElementById('bank-info').style.display = 'none';
                document.getElementById('ewallet-info').style.display = 'none';
                
                if (method === 'Transfer Bank') {
                    document.getElementById('bank-info').style.display = 'block';
                } else if (method === 'E-Wallet') {
                    document.getElementById('ewallet-info').style.display = 'block';
                }
            }
            </script>
            
            <div class="form-group">
                <label for="keterangan">Keterangan (Opsional)</label>
                <textarea id="keterangan" name="keterangan" class="form-control" rows="3" placeholder="Tambahkan catatan jika ada..."><?= htmlspecialchars(old('keterangan', '')) ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Konfirmasi Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
