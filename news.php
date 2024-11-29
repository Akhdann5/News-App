<?php
include 'includes/db.php';

// Fetch All News
$stmt = $conn->prepare("SELECT * FROM news ORDER BY created_at DESC");
$stmt->execute();
$newsList = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News</title>
    <link rel="stylesheet" href="css/news.css">
</head>
<body>
    <div class="container">
        <h1>Latest News</h1>
        
        <?php if (count($newsList) > 0): ?>
            <?php foreach ($newsList as $news): ?>
                <div class="news-item">
                    <h2><?php echo htmlspecialchars($news['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($news['content'])); ?></p>
                    <span class="news-date"><?php echo date('F j, Y, g:i a', strtotime($news['created_at'])); ?></span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No news available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
