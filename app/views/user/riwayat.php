<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="riwayat-container">
    <div class="page-header">
        <h1>Riwayat Booking</h1>
        <p>Daftar booking yang telah Anda lakukan</p>
    </div>
    
    <div class="riwayat-content">
        <?php if (!empty($bookings)): ?>
            <div class="table-responsive">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Booking</th>
                            <th>Studio</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Total Bayar</th>
                            <th>Status Booking</th>
                            <th>Bukti Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong>#<?= $booking['id_booking'] ?></strong></td>
                                <td><?= htmlspecialchars($booking['nama_studio']) ?></td>
                                <td><?= date('d/m/Y', strtotime($booking['tanggal_main'])) ?></td>
                                <td><?= date('H:i', strtotime($booking['jam_mulai'])) ?> - <?= date('H:i', strtotime($booking['jam_selesai'])) ?></td>
                                <td>Rp. <?= number_format($booking['total_bayar'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $booking['status_booking'])) ?>">
                                        <?= htmlspecialchars($booking['status_booking']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (isset($booking['payment']) && !empty($booking['payment']['bukti_pembayaran'])): ?>
                                        <a href="/Studio-Music/public/images/payments/<?= htmlspecialchars($booking['payment']['bukti_pembayaran']) ?>" 
                                           target="_blank" 
                                           class="btn-view-bukti">
                                            📄 Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum upload</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($booking['status_booking'] == 'Menunggu Konfirmasi'): ?>
                                        <form method="POST" action="/Studio-Music/public/index.php?url=user/cancelBooking" style="display: inline;">
                                            <input type="hidden" name="id_booking" value="<?= $booking['id_booking'] ?>">
                                            <button type="submit" class="btn-delete" onclick="return confirm('Yakin ingin membatalkan booking ini?')">
                                                Batalkan
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>Anda belum memiliki riwayat booking.</p>
                <a href="/Studio-Music/public/index.php?url=user/dashboard" class="btn btn-primary">Booking Sekarang</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
