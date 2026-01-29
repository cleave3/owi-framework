<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post->title) ?></title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .meta { color: #666; font-size: 0.9em; margin-bottom: 20px; }
        .btn { display:inline-block; margin-top: 20px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($post->title) ?></h1>
    <div class="meta">
        Category: <strong><?= htmlspecialchars($post->category->name ?? 'Uncategorized') ?></strong> | 
        Author: <?= htmlspecialchars($post->author->name ?? 'Unknown') ?> | 
        Published: <?= $post->created_at ?>
    </div>
    
    <div class="content">
        <?= nl2br(htmlspecialchars($post->content)) ?>
    </div>

    <a href="/posts" class="btn">&larr; Back to Posts</a>
</body>
</html>
