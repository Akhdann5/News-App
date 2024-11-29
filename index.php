<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="#">NEWS APP</a>
        </div>
        <ul class="nav-links">
            <?php if (isset($_SESSION['user'])): ?>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <li><a href="make-news.php">Manage News</a></li>
                <li><a href="manage-users.php">Manage Users</a></li>
            <?php else: ?>
                <li><a href="make-news.php">My News</a></li>
                <li><a href="news.php">See News</a></li>
            <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="news.php">See News</a></li>
            <?php endif; ?>
        </ul>

    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <h1>NEWS APP</h1>
        <p>Selamat datang dan buat atau lihat berita-berita baru disini.</p>
    </div>
</body>
</html>

