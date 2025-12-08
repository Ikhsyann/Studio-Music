<?php include __DIR__ . '/header.php'; ?>

<div class="admin-dashboard-container">
    <div class="page-header">
        <h1><?= isset($studio) ? 'Edit Studio' : 'Tambah Studio Baru' ?></h1>
        <p>Lengkapi form di bawah ini</p>
    </div>
    
    <div class="admin-section">
        <form method="POST" action="/Studio-Music/public/index.php?url=admin/saveStudio" class="studio-form" enctype="multipart/form-data">
            <?php if (isset($studio)): ?>
                <input type="hidden" name="id_studio" value="<?= $studio['id_studio'] ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nama_studio">Nama Studio <span style="color: red;">*</span></label>
                          <input type="text" id="nama_studio" name="nama_studio" class="form-control" 
                              value="<?= htmlspecialchars(old('nama_studio', isset($studio) ? $studio['nama_studio'] : '')) ?>" 
                              required>
                </div>
                
                <div class="form-group">
                    <label for="harga_per_jam">Harga per Jam (Rp) <span style="color: red;">*</span></label>
                          <input type="number" id="harga_per_jam" name="harga_per_jam" class="form-control" 
                              value="<?= htmlspecialchars(old('harga_per_jam', isset($studio) ? $studio['harga_per_jam'] : '')) ?>" 
                              required min="0" step="1000">
                </div>
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" maxlength="150"><?= htmlspecialchars(old('deskripsi', isset($studio) ? $studio['deskripsi'] : '')) ?></textarea>
                <small>Deskripsi maksimal 150 karakter.</small>
            </div>
            
            <div class="form-group">
                <label for="fasilitas">Fasilitas</label>
                <textarea id="fasilitas" name="fasilitas" class="form-control" rows="3" 
                          placeholder="Contoh: Microphone Condenser, Audio Interface, Drum Set, Keyboard"><?= htmlspecialchars(old('fasilitas', isset($studio) ? $studio['fasilitas'] : '')) ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="kapasitas">Kapasitas (orang)</label>
                          <input type="number" id="kapasitas" name="kapasitas" class="form-control" 
                              value="<?= htmlspecialchars(old('kapasitas', isset($studio) ? $studio['kapasitas'] : '')) ?>" 
                              min="1">
                </div>
                
                <div class="form-group">
                    <label for="status_ketersediaan">Status Ketersediaan</label>
                    <select id="status_ketersediaan" name="status_ketersediaan" class="form-control">
                        <option value="Tersedia" <?= old('status_ketersediaan', isset($studio) ? $studio['status_ketersediaan'] : 'Tersedia') == 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                        <option value="Tidak Tersedia" <?= old('status_ketersediaan', isset($studio) ? $studio['status_ketersediaan'] : 'Tersedia') == 'Tidak Tersedia' ? 'selected' : '' ?>>Tidak Tersedia</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="gambar_file">Gambar Studio</label>
                <?php if (isset($studio) && !empty($studio['gambar'])): ?>
                    <div style="margin-bottom:8px;">
                        <img src="/Studio-Music/public/images/<?= htmlspecialchars($studio['gambar']) ?>" alt="<?= htmlspecialchars($studio['nama_studio']) ?>" style="max-width:200px; border:1px solid #ddd; padding:4px;">
                    </div>
                <?php endif; ?>
                <input type="file" id="gambar_file" name="gambar_file" class="form-control" accept="image/jpeg,image/png" <?= isset($studio) ? '' : 'required' ?>>
                <small>File akan otomatis dinamai berdasarkan nama studio (mis. <em>nama-studio.jpg</em>). Format: JPG/PNG. Maks 5MB.</small>
                <?php if (isset($studio)): ?>
                    <input type="hidden" name="existing_gambar" value="<?= htmlspecialchars($studio['gambar']) ?>">
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= isset($studio) ? 'Update Studio' : 'Tambah Studio' ?>
                </button>
                <a href="/Studio-Music/public/index.php?url=admin/studios" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.studio-form {
    max-width: 800px;
    margin: 0 auto;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}

.btn-secondary {
    background: #f5f5f5;
    color: #666;
    padding: 10px 20px;
    border: 1px solid #ddd;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
}

.btn-secondary:hover {
    background: #e8e8e8;
}
</style>

<?php include __DIR__ . '/footer.php'; ?>
