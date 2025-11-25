<?php include __DIR__ . '/header.php'; ?>

<div class="admin-dashboard-container">
    <div class="page-header">
        <h1>Manajemen User</h1>
        <p>Kelola akun pengguna</p>
    </div>
    
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
            <?php 
            echo $_SESSION['flash']['message'];
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>
    
    <!-- User Table -->
    <div class="admin-section">
        <h2>Semua User</h2>
        
        <?php if (!empty($users)): ?>
            <div class="table-responsive">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID User</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No Telepon</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong>#<?= $user['id_user'] ?></strong></td>
                                <td><?= htmlspecialchars($user['nama']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['no_telp']) ?></td>
                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <form method="POST" action="/Studio-Music/public/index.php?url=admin/deleteUser" style="display: inline;">
                                        <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">
                                        <button type="submit" class="btn-delete" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>Belum ada user terdaftar.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
