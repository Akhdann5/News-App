<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password
    $role = 'writer'; // Default role

    // Periksa apakah email sudah digunakan
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $error = "Email already registered. Please use a different email.";
    } else {
        // Simpan pengguna ke database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);

        // Redirect ke halaman login
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="register.php">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <p>Sudah punya akun? Ayo <a href="login.php">login disini</a>.</p>
    </div>
</body>
</html>
