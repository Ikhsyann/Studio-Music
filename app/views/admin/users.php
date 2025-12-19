<?php include __DIR__ . '/header.php'; ?>

<div class="admin-dashboard-container">
    <div class="page-header">
        <h1>Manajemen User & Admin</h1>
        <p>Kelola akun pengguna dan administrator</p>
    </div>
    
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
            <?php 
            echo $_SESSION['flash']['message'];
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
        <!-- User Table (Left) -->
        <div class="admin-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Daftar User</h2>
                <a href="/Studio-Music/public/index.php?url=admin/addUser" class="btn btn-primary">
                    Tambah User
                </a>
            </div>
            
            <?php if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No Telp</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($user['nama']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['no_telp']) ?></td>
                                    <td>
                                        <a href="/Studio-Music/public/index.php?url=admin/editUser/<?= $user['id_user'] ?>" class="btn-edit" style="margin-bottom: 5px;">
                                            Edit
                                        </a>
                                        <form method="POST" action="/Studio-Music/public/index.php?url=admin/deleteUser" style="display: inline;">
                                            <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">
                                            <button type="submit" class="btn-delete" onclick="return confirm('Yakin hapus user ini?')">
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
                <div class="empty-state">
                    <div class="empty-state-icon">👤</div>
                    <h3>Belum Ada User</h3>
                    <p>Belum ada user yang terdaftar. User dapat mendaftar sendiri atau Anda dapat menambahkan secara manual.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Admin Table (Right) -->
        <div class="admin-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Daftar Admin</h2>
                <a href="/Studio-Music/public/index.php?url=admin/addAdmin" class="btn btn-primary">
                    Tambah Admin
                </a>
            </div>
            
            <?php if (!empty($admins)): ?>
                <div class="table-responsive">
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Email</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($admins as $admin_item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($admin_item['email']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($admin_item['created_at'])) ?></td>
                                    <td>
                                        <form method="POST" action="/Studio-Music/public/index.php?url=admin/deleteAdmin" style="display: inline;">
                                            <input type="hidden" name="id_admin" value="<?= $admin_item['id_admin'] ?>">
                                            <button type="submit" class="btn-delete" onclick="return confirm('Yakin hapus admin ini?')">
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
                <div class="empty-state">
                    <div class="empty-state-icon">🔐</div>
                    <h3>Belum Ada Admin</h3>
                    <p>Tambahkan admin untuk membantu mengelola booking dan studio.</p>
                    <a href="/Studio-Music/public/index.php?url=admin/addAdmin" class="btn btn-primary">
                        Tambah Admin Baru
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
