<?php include __DIR__ . '/header.php'; ?>

<div class="admin-dashboard-container">
    <div class="page-header">
        <h1>Kelola Studio</h1>
        <p>Daftar semua studio musik</p>
    </div>
    
    <div class="admin-section">
        <div style="margin-bottom: 20px;">
            <a href="/Studio-Music/public/index.php?url=admin/addStudio" class="btn btn-primary">
                Tambah Studio Baru
            </a>
        </div>
        
        <?php if (!empty($studios)): ?>
            <div class="table-responsive">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Studio</th>
                            <th>Deskripsi</th>
                            <th>Harga/Jam</th>
                            <th>Kapasitas</th>
                            <th>Fasilitas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($studios as $studio): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($studio['nama_studio']) ?></strong></td>
                                <td><?= htmlspecialchars(substr($studio['deskripsi'], 0, 50)) ?>...</td>
                                <td>Rp. <?= number_format($studio['harga_per_jam'], 0, ',', '.') ?></td>
                                <td><?= $studio['kapasitas'] ?> orang</td>
                                <td><?= htmlspecialchars(substr($studio['fasilitas'], 0, 40)) ?>...</td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($studio['status_ketersediaan']) ?>">
                                        <?= htmlspecialchars($studio['status_ketersediaan']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/Studio-Music/public/index.php?url=admin/editStudio/<?= $studio['id_studio'] ?>" 
                                           class="btn-approve">
                                            Edit
                                        </a>
                                        <form method="POST" action="/Studio-Music/public/index.php?url=admin/deleteStudio" style="display: inline;">
                                            <input type="hidden" name="id_studio" value="<?= $studio['id_studio'] ?>">
                                            <button type="submit" class="btn-reject" onclick="return confirm('Yakin hapus studio ini?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">🎵</div>
                <h3>Belum Ada Studio</h3>
                <p>Tambahkan studio musik pertama Anda untuk mulai menerima booking dari pengguna.</p>
                <a href="/Studio-Music/public/index.php?url=admin/addStudio" class="btn btn-primary">
                    Tambah Studio Baru
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
