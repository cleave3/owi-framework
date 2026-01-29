<?php

namespace App\Controllers;

use App\Services\PostService;
use Owi\utils\Response;
use Owi\utils\DB; // For seeding categories/users if needed quickly in create view

class PostController extends Controller
{
    protected $postService;

    public function __construct()
    {
        // Manual DI for now since our Container might expect strict typing or setup
        $this->postService = new PostService();
    }

    public function index()
    {
        $posts = $this->postService->getAllPosts();
        echo view('posts/index', ['posts' => $posts]);
    }

    public function show($id)
    {
        $post = $this->postService->getPostById($id);
        if (!$post) {
            return Response::json(['error' => 'Post not found'], 404);
        }

        echo view('posts/show', ['post' => $post]);
    }

    public function create()
    {
        // Get categories and users for the dropdowns
        $categories = DB::table('categories')->get();
        $users = DB::table('users')->get();

        echo view('posts/create', ['categories' => $categories, 'users' => $users]);
    }

    public function store()
    {
        // Assume $_POST is populated
        $result = $this->postService->createPost($_POST);

        if ($result['status']) {
            // Redirect or JSON
            header('Location: /posts');
            exit;
        } else {
            return Response::json($result, 400);
        }
    }
    public function apiIndex()
    {
        $posts = $this->postService->getAllPosts();
        return Response::json($posts);
    }

    public function apiShow($id)
    {
        $post = $this->postService->getPostById($id);
        
        if ($post) {
            return Response::json($post);
        }
        
        return Response::json(['error' => 'Post not found'], 404);
    }
}
