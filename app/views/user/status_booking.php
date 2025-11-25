<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="status-booking-container">
    <div class="page-header">
        <h1>Status Booking - Jadwal Studio</h1>
        <p>Lihat semua jadwal booking studio yang tersedia</p>
    </div>
    
    <!-- Filter by Date -->
    <div class="filter-section">
        <form method="GET" class="filter-form">
            <input type="hidden" name="url" value="user/statusBooking">
            <div class="form-row">
                <div class="form-group">
                    <label>Filter Tanggal:</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $_GET['tanggal'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="/Studio-Music/public/index.php?url=user/statusBooking" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
    
    <div class="status-content">
        <?php if (!empty($bookings)): ?>
            <div class="table-responsive">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Studio</th>
                            <th>Nama User</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Durasi</th>
                            <th>Total Bayar</th>
                            <th>Status Booking</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($booking['nama_studio']) ?></strong></td>
                                <td><?= htmlspecialchars($booking['nama_user']) ?></td>
                                <td><?= date('d F Y', strtotime($booking['tanggal_main'])) ?></td>
                                <td><?= date('H:i', strtotime($booking['jam_mulai'])) ?> - <?= date('H:i', strtotime($booking['jam_selesai'])) ?></td>
                                <td>
                                    <?php
                                    $start = strtotime($booking['jam_mulai']);
                                    $end = strtotime($booking['jam_selesai']);
                                    $duration = ($end - $start) / 3600;
                                    echo $duration . ' jam';
                                    ?>
                                </td>
                                <td>Rp. <?= number_format($booking['total_bayar'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $booking['status_booking'])) ?>">
                                        <?= htmlspecialchars($booking['status_booking']) ?>
                                    </span>
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>
