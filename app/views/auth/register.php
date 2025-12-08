<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Studio Musik</title>
    <link rel="stylesheet" href="/Studio-Music/public/css/minimalist.css">
    <link rel="stylesheet" href="/Studio-Music/public/css/auth-minimalist.css">
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
            
            <form action="/Studio-Music/public/index.php?url=auth/registerProcess" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-control" required value="<?= htmlspecialchars(old('nama', '')) ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars(old('email', '')) ?>">
                </div>
                
                <div class="form-group">
                    <label for="no_telp">No. Telepon</label>
                    <input type="tel" id="no_telp" name="no_telp" class="form-control" required value="<?= htmlspecialchars(old('no_telp', '')) ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            </form>
            
            <div class="auth-footer">
                <p>Sudah punya akun? <a href="/Studio-Music/public/index.php?url=auth/login">Login di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>
