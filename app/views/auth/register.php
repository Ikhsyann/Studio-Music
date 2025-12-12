<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Studio Musik</title>
    <link rel="stylesheet" href="/Studio-Music/public/css/main.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Studio Musik</h1>
                <p>Daftar akun baru</p>
            </div>
            
            <?php
            if (isset($_SESSION['flash'])) {
                $flash = $_SESSION['flash'];
                unset($_SESSION['flash']);
                echo '<div class="alert alert-' . $flash['type'] . '">' . $flash['message'] . '</div>';
            }
            ?>
            
            <form action="/Studio-Music/public/index.php?url=auth/registerProcess" method="POST" class="auth-form" id="registerForm" onsubmit="return validateRegisterForm()">
                <div class="form-group">
                    <label for="nama">Nama Lengkap <span style="color: #EF5350;">*</span></label>
                    <input type="text" id="nama" name="nama" class="form-control" required 
                           minlength="3" maxlength="100" 
                           pattern="[A-Za-z\s]+" 
                           title="Nama hanya boleh berisi huruf dan spasi"
                           value="<?= htmlspecialchars(old('nama', '')) ?>">
                    <small style="color: #C4C4C4;">Minimal 3 karakter, hanya huruf</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email <span style="color: #EF5350;">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                           title="Masukkan email yang valid (contoh: user@example.com)"
                           value="<?= htmlspecialchars(old('email', '')) ?>">
                    <small style="color: #C4C4C4;">Format: user@example.com</small>
                </div>
                
                <div class="form-group">
                    <label for="no_telp">No. Telepon <span style="color: #EF5350;">*</span></label>
                    <input type="tel" id="no_telp" name="no_telp" class="form-control" required 
                           pattern="[0-9]{10,15}" 
                           minlength="10" maxlength="15"
                           title="Nomor telepon harus 10-15 digit angka"
                           value="<?= htmlspecialchars(old('no_telp', '')) ?>">
                    <small style="color: #C4C4C4;">10-15 digit angka (contoh: 081234567890)</small>
                </div>
                
                <div class="form-group">
                    <label for="password">Password <span style="color: #EF5350;">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" required 
                           minlength="6" maxlength="50"
                           title="Password minimal 6 karakter">
                    <small style="color: #C4C4C4;">Minimal 6 karakter</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password <span style="color: #EF5350;">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required 
                           minlength="6" maxlength="50"
                           title="Konfirmasi password harus sama dengan password">
                    <small id="password-match" style="color: #C4C4C4;">Ulangi password yang sama</small>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            </form>
            
            <script>
            function validateRegisterForm() {
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
                    document.getElementById('password-match').style.color = '#EF5350';
                    document.getElementById('password-match').textContent = '❌ Password tidak sama!';
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
                    matchText.style.color = '#C4C4C4';
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
            
            <div class="auth-footer">
                <p>Sudah punya akun? <a href="/Studio-Music/public/index.php?url=auth/login">Login di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>
