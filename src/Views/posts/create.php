<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 150px; }
        button { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2980b9; }
    </style>
</head>
<body>
    <h1>Create New Post</h1>
    
    <form action="/posts/store" method="POST">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category_id" required>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Author</label>
            <select name="user_id" required>
                <?php foreach($users as $user): ?>
                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Content</label>
            <textarea name="content" required></textarea>
        </div>

        <button type="submit">Publish Post</button>
    </form>
</body>
</html>
