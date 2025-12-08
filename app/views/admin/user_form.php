<?php include __DIR__ . '/header.php'; ?>

<div class="admin-dashboard-container">
    <div class="page-header">
        <h1>Tambah User Baru</h1>
        <p>Lengkapi form di bawah ini</p>
    </div>
    
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
            <?php 
            echo $_SESSION['flash']['message'];
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="admin-section">
        <form method="POST" action="/Studio-Music/public/index.php?url=admin/saveUser" class="studio-form">
            <div class="form-group">
                <label for="nama">Nama Lengkap <span style="color: red;">*</span></label>
                  <input type="text" id="nama" name="nama" class="form-control" required value="<?= htmlspecialchars(old('nama', '')) ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email <span style="color: red;">*</span></label>
                 <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars(old('email', '')) ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password <span style="color: red;">*</span></label>
                 <input type="password" id="password" name="password" class="form-control" required minlength="3">
            </div>
            
            <div class="form-group">
                <label for="no_telp">No Telepon <span style="color: red;">*</span></label>
                    <input type="text" id="no_telp" name="no_telp" class="form-control" required pattern="[0-9]{10,15}" value="<?= htmlspecialchars(old('no_telp', '')) ?>">
                <small>Format: 10-15 digit angka</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Tambah User
                </button>
                <a href="/Studio-Music/public/index.php?url=admin/users" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.studio-form {
    max-width: 600px;
    margin: 0 auto;
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

small {
    color: #666;
    font-size: 12px;
}
</style>

<?php include __DIR__ . '/footer.php'; ?>
