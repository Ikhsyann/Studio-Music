<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?></title>
    <link rel="stylesheet" href="/Studio-Music/public/css/main.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <h2>Admin Panel</h2>
            </div>
            <ul class="navbar-menu">
                <li><a href="/Studio-Music/public/index.php?url=admin/dashboard" class="nav-link">Dashboard</a></li>
                <li><a href="/Studio-Music/public/index.php?url=admin/studios" class="nav-link">Kelola Studio</a></li>
                <li><a href="/Studio-Music/public/index.php?url=admin/users" class="nav-link">Kelola Akun</a></li>
                <li><a href="/Studio-Music/public/index.php?url=admin/logout" class="nav-link logout">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <?php
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . $flash['type'] . '">' . $flash['message'] . '</div>';
    }
    ?>
    
    <main class="main-content">
