<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owi Framework - Posts</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .post { border-bottom: 1px solid #eee; padding: 20px 0; }
        .meta { color: #666; font-size: 0.9em; }
        a { color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { background: #3498db; color: white; padding: 10px 15px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Blog Posts</h1>
        <a href="/posts/create" class="btn">Create New Post</a>
    </div>

    <?php if (empty($posts)): ?>
        <p>No posts found.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h2><a href="/posts/<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                <div class="meta">
                    Category: <strong><?= htmlspecialchars($post['category_name'] ?? 'Uncategorized') ?></strong> | 
                    Author: <?= htmlspecialchars($post['author_name'] ?? 'Unknown') ?> | 
                    Date: <?= $post['created_at'] ?>
                </div>
                <p><?= substr(htmlspecialchars($post['content']), 0, 150) ?>...</p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
