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
        <form method="POST" action="/Studio-Music/public/index.php?url=admin/saveUser" class="studio-form" id="userForm" onsubmit="return validateUserForm()">
            <div class="form-group">
                <label for="nama">Nama Lengkap <span style="color: red;">*</span></label>
                <input type="text" id="nama" name="nama" class="form-control" required 
                       minlength="3" maxlength="100"
                       pattern="[A-Za-z\s]+"
                       title="Nama hanya boleh berisi huruf dan spasi"
                       value="<?= htmlspecialchars(old('nama', '')) ?>">
                <small>Minimal 3 karakter, hanya huruf dan spasi</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email <span style="color: red;">*</span></label>
                <input type="email" id="email" name="email" class="form-control" required 
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                       title="Masukkan email yang valid"
                       value="<?= htmlspecialchars(old('email', '')) ?>">
                <small>Format: user@example.com</small>
            </div>
            
            <div class="form-group">
                <label for="password">Password <span style="color: red;">*</span></label>
                <input type="password" id="password" name="password" class="form-control" required 
                       minlength="6" maxlength="50"
                       title="Password minimal 6 karakter">
                <small>Minimal 6 karakter</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password <span style="color: red;">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required 
                       minlength="6" maxlength="50">
                <small id="password-match">Ulangi password yang sama</small>
            </div>
            
            <div class="form-group">
                <label for="no_telp">No Telepon <span style="color: red;">*</span></label>
                <input type="tel" id="no_telp" name="no_telp" class="form-control" required 
                       pattern="[0-9]{10,15}" 
                       minlength="10" maxlength="15"
                       title="Nomor telepon harus 10-15 digit angka"
                       value="<?= htmlspecialchars(old('no_telp', '')) ?>">
                <small>10-15 digit angka (contoh: 081234567890)</small>
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
    
    <script>
    function validateUserForm() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const nama = document.getElementById('nama').value;
        const email = document.getElementById('email').value;
        const noTelp = document.getElementById('no_telp').value;
        
        // Validate name
        if (!/^[A-Za-z\s]+$/.test(nama)) {
            alert('Nama hanya boleh berisi huruf dan spasi!');
            return false;
        }
        
        if (nama.length < 3) {
            alert('Nama minimal 3 karakter!');
            return false;
        }
        
        // Validate email
        const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
        if (!emailPattern.test(email.toLowerCase())) {
            alert('Format email tidak valid!');
            return false;
        }
        
        // Validate phone
        if (!/^[0-9]{10,15}$/.test(noTelp)) {
            alert('Nomor telepon harus 10-15 digit angka!');
            return false;
        }
        
        // Validate password
        if (password.length < 6) {
            alert('Password minimal 6 karakter!');
            return false;
        }
        
        // Check password match
        if (password !== confirmPassword) {
            alert('Password dan Konfirmasi Password tidak sama!');
            return false;
        }
        
        return true;
    }
    
    // Real-time password match check
    document.getElementById('confirm_password').addEventListener('keyup', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        const matchText = document.getElementById('password-match');
        
        if (confirmPassword === '') {
            matchText.style.color = '#666';
            matchText.textContent = 'Ulangi password yang sama';
        } else if (password === confirmPassword) {
            matchText.style.color = '#34D399';
            matchText.textContent = '✓ Password cocok';
        } else {
            matchText.style.color = '#EF5350';
            matchText.textContent = '❌ Password tidak sama';
        }
    });
    </script>
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
