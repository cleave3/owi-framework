<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; padding: 40px; background: #f9f9f9; color: #333; }
        h1 { margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        th, td { text-align: left; padding: 15px 20px; border-bottom: 1px solid #eee; }
        th { background-color: #f4f5f7; font-weight: 600; text-transform: uppercase; font-size: 0.85em; letter-spacing: 0.05em; color: #555; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #fafafa; }
        .method { font-weight: bold; padding: 5px 10px; border-radius: 4px; font-size: 0.8em; display: inline-block; width: 60px; text-align: center; }
        .method-GET { background-color: #e3f2fd; color: #1976d2; }
        .method-POST { background-color: #e8f5e9; color: #388e3c; }
        .method-PUT { background-color: #fff3e0; color: #f57c00; }
        .method-DELETE { background-color: #ffebee; color: #d32f2f; }
        .method-ANY { background-color: #f3e5f5; color: #7b1fa2; }
        .path { font-family: monospace; font-size: 1.1em; color: #333; }
        .handler { color: #888; font-size: 0.9em; font-family: monospace; }
        .empty { padding: 40px; text-align: center; color: #999; }
    </style>
</head>
<body>
    <h1>API Routes Documentation</h1>

    <?php if (empty($routes)): ?>
        <div class="empty">No routes registered.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th width="100">Method</th>
                    <th>Path</th>
                    <th>Handler</th>
                    <th>Middleware</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $method => $paths): ?>
                    <?php foreach ($paths as $path => $details): ?>
                        <tr>
                            <td><span class="method method-<?= $method ?>"><?= $method ?></span></td>
                            <td class="path"><?= htmlspecialchars($path) ?></td>
                            <td class="handler">
                                <?php 
                                    if (is_array($details['callback'])) {
                                        echo htmlspecialchars(implode('::', $details['callback']));
                                    } elseif (is_string($details['callback'])) {
                                        echo htmlspecialchars($details['callback']);
                                    } else {
                                        echo 'Closure';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php echo !empty($details['middleware']) ? implode(', ', $details['middleware']) : '-'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
