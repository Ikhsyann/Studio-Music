<?php include __DIR__ . '/header.php'; ?>

<div class="admin-dashboard-container">
    <div class="page-header">
        <h1>Dashboard Admin</h1>
        <p>Selamat datang, <?= htmlspecialchars($admin['email']) ?>!</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <h3><?= $totalBookings ?></h3>
                <p>Total Booking</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon"></div>
            <div class="stat-info">
                <h3><?= $totalStudios ?></h3>
                <p>Total Studio</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?= $totalUsers ?></h3>
                <p>Total User</p>
            </div>
        </div>
    </div>
    
    <!-- Recent Bookings -->
    <div class="admin-section">
        <h2>Semua Booking</h2>
        
        <?php if (!empty($bookings)): ?>
            <div class="table-responsive">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Booking</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Studio</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Total</th>
                            <th>Status Booking</th>
                            <th>Bukti Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong>#<?= $booking['id_booking'] ?></strong></td>
                                <td><?= htmlspecialchars($booking['nama_user']) ?></td>
                                <td><?= htmlspecialchars($booking['email_user']) ?></td>
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
                                    <?php if (!empty($booking['bukti_pembayaran'])): ?>
                                        <a href="/Studio-Music/public/images/payments/<?= htmlspecialchars($booking['bukti_pembayaran']) ?>" 
                                           target="_blank" 
                                           class="btn-view-bukti">
                                            📄 Lihat Bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum upload</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($booking['status_booking'] === 'Menunggu Konfirmasi'): ?>
                                        <div class="action-buttons">
                                            <form method="POST" action="/Studio-Music/public/index.php?url=admin/approveBooking" style="display: inline;">
                                                <input type="hidden" name="id_booking" value="<?= $booking['id_booking'] ?>">
                                                <button type="submit" class="btn-approve" onclick="return confirm('Setujui booking ini?')">
                                                    ✓ Setujui
                                                </button>
                                            </form>
                                            <form method="POST" action="/Studio-Music/public/index.php?url=admin/rejectBooking" style="display: inline;">
                                                <input type="hidden" name="id_booking" value="<?= $booking['id_booking'] ?>">
                                                <button type="submit" class="btn-reject" onclick="return confirm('Tolak booking ini?')">
                                                    ✗ Tolak
                                                </button>
                                            </form>
                                        </div>
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
                <p>Belum ada booking di sistem.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
