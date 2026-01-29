<?php

namespace App\Services;

use App\Models\Post;
use Owi\utils\DB;
use Owi\utils\Validator;

class PostService
{
    /**
     * Get all posts with user and category info
     */
    public function getAllPosts()
    {
        // Using raw query for joins as Eloquent-like 'with' might not be fully implemented in our simple Model
        // Or we can use the loop approach if lazy loading works.
        // Let's try to use the relationship methods if possible, 
        // but for a list view, a join is more efficient.
        // Since our ORM is simple, let's use DB::query to show off raw SQL power too.
        
        $sql = "SELECT p.*, u.name as author_name, c.name as category_name 
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
                
        return DB::query($sql)->fetchAll();
    }

    public function getPostById($id)
    {
        $post = (new Post())->find($id);
        if ($post) {
            // Manually load relations for demo
            $post->author = (new \App\Models\User())->find($post->user_id);
            $post->category = (new \App\Models\Category())->find($post->category_id);
            return $post;
        }
        return null;
    }

    public function createPost($data)
    {
        // Validate
        $validator = Validator::validate([
            Validator::owi($data['title'] ?? '')->required()->string()->exec(),
            Validator::owi($data['content'] ?? '')->required()->exec(),
            Validator::owi($data['user_id'] ?? '')->required()->integer()->exec(),
            Validator::owi($data['category_id'] ?? '')->required()->integer()->exec(),
        ]);

        if (!$validator['isvalid']) {
            return ['status' => false, 'errors' => $validator['errors']];
        }

        $post = new Post();
        $post->title = $data['title'];
        $post->slug = strtolower(str_replace(' ', '-', $data['title']));
        $post->content = $data['content'];
        $post->user_id = $data['user_id'];
        $post->category_id = $data['category_id'];
        
        if ($post->save()) {
            return ['status' => true, 'id' => $post->id];
        }

        return ['status' => false, 'errors' => ['Failed to save post']];
    }
}
