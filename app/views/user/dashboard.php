<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-container">
    <div class="page-header">
        <h2>Studio Musik</h2>
        <p class="subtitle">Booking yang baik, suara yang merdu</p>
        <p class="subtitle">Izinn........</p>
    </div>
    
    <div class="studio-grid">
        <?php if (!empty($studios)): ?>
            <?php foreach ($studios as $studio): ?>
                <div class="studio-card-modern">
                    <div class="studio-card-image-wrapper">
                        <img src="/Studio-Music/public/images/<?= htmlspecialchars($studio['gambar'] ?? 'default-studio.jpg') ?>" alt="<?= htmlspecialchars($studio['nama_studio']) ?>" class="studio-image-modern">
                        
                        <div class="studio-overlay">
                            <div class="studio-location">
                                <?= htmlspecialchars($studio['nama_studio']) ?>
                            </div>
                            <h3 class="studio-title-overlay"><?= htmlspecialchars($studio['nama_studio']) ?></h3>
                            
                            <div class="studio-details-overlay">
                                <p class="studio-description">
                                    <?= htmlspecialchars($studio['deskripsi'] ?? 'Studio musik berkualitas dengan fasilitas lengkap') ?>
                                </p>
                                <div class="studio-facilities">
                                    <p><strong>Fasilitas:</strong></p>
                                    <p><?= htmlspecialchars($studio['fasilitas']) ?></p>
                                </div>
                                <div class="studio-capacity">
                                    <p><strong>Kapasitas:</strong> <?= $studio['kapasitas'] ?> orang</p>
                                </div>
                            </div>
                            
                            <div class="studio-footer-overlay">
                                <div class="studio-price-overlay">
                                    <div class="price-label">Start From</div>
                                    <div class="price-amount">Rp. <?= number_format($studio['harga_per_jam'], 0, ',', '.') ?> / Sesi</div>
                                </div>
                                <a href="/Studio-Music/public/index.php?url=user/booking/<?= $studio['id_studio'] ?>" class="btn-booking-overlay">
                                    Booking
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-data">
                <p>Belum ada studio tersedia saat ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
