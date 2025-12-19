<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Studio Musik' ?></title>
    <link rel="stylesheet" href="/Studio-Music/public/css/style.css">
    <link rel="stylesheet" href="/Studio-Music/public/css/main.css?v=2">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <h2><?= $user['nama'] ?? 'Studio Musik' ?></h2>
            </div>
            <ul class="navbar-menu">
                <li><a href="/Studio-Music/public/index.php?url=user/dashboard" class="nav-link">Dashboard</a></li>
                <li><a href="/Studio-Music/public/index.php?url=user/riwayat" class="nav-link">Riwayat Booking</a></li>
                <li><a href="/Studio-Music/public/index.php?url=user/statusBooking" class="nav-link">Booked Studio</a></li>
                <li><a href="/Studio-Music/public/index.php?url=auth/logout" class="nav-link logout">Logout</a></li>
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
