<?php
session_start();
include 'includes/db.php';

// Redirect jika user belum login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// User info dari session
$user = $_SESSION['user'];

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $userId = $user['id'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];

        // Update hanya jika admin atau pemilik berita
        if ($user['role'] === 'admin' || isNewsOwner($id, $userId)) {
            $stmt = $conn->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
            $stmt->execute([$title, $content, $id]);
            $message = "News updated successfully!";
        } else {
            $error = "You are not authorized to update this news.";
        }
    } else {
        // Buat berita baru
        $stmt = $conn->prepare("INSERT INTO news (title, content, created_at, user_id) VALUES (?, ?, NOW(), ?)");
        $stmt->execute([$title, $content, $userId]);
        $message = "News created successfully!";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Hapus hanya jika admin atau pemilik berita
    if ($user['role'] === 'admin' || isNewsOwner($id, $user['id'])) {
        $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $message = "News deleted successfully!";
    } else {
        $error = "You are not authorized to delete this news.";
    }
}

// Fungsi untuk memeriksa kepemilikan berita
function isNewsOwner($newsId, $userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM news WHERE id = ? AND user_id = ?");
    $stmt->execute([$newsId, $userId]);
    return $stmt->fetch() !== false;
}

// Fetch All News
$stmt = $conn->prepare($user['role'] === 'admin' 
    ? "SELECT * FROM news ORDER BY created_at DESC" // Admin melihat semua berita
    : "SELECT * FROM news WHERE user_id = ? ORDER BY created_at DESC"); // Penulis melihat berita mereka
$user['role'] === 'admin' ? $stmt->execute() : $stmt->execute([$user['id']]);
$newsList = $stmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $userId = $_SESSION['user']['id']; // Ambil user_id dari session

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update berita
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$title, $content, $id]);
        $message = "News updated successfully!";
    } else {
        // Buat berita baru
        $stmt = $conn->prepare("INSERT INTO news (title, content, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $userId]);
        $message = "News created successfully!";
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News</title>
    <link rel="stylesheet" href="css/make-news.css">
</head>
<body>
    <div class="container">
        <h1>Manage News</h1>
        
        <?php if (isset($message)) echo "<p class='success'>$message</p>"; ?>

        <!-- Form to Create or Edit News -->
        <form method="POST" action="make-news.php">
            <input type="hidden" name="id" id="news-id">
            <input type="text" name="title" id="news-title" placeholder="News Title" required>
            <textarea name="content" id="news-content" placeholder="News Content" required></textarea>
            <button type="submit">Save</button>
        </form>

        <!-- List of News -->
        <h2>All News</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($newsList as $news): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($news['title']); ?></td>
                        <td><?php echo htmlspecialchars(substr($news['content'], 0, 100)) . '...'; ?></td>
                        <td>
                            <button onclick="editNews(<?php echo htmlspecialchars(json_encode($news)); ?>)">Edit</button>
                            <a href="make-news.php?delete=<?php echo $news['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editNews(news) {
            document.getElementById('news-id').value = news.id;
            document.getElementById('news-title').value = news.title;
            document.getElementById('news-content').value = news.content;
        }
    </script>
</body>
</html>
