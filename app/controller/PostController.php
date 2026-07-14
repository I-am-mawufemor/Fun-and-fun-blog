<?php

namespace Mawufemor\Techandfun\Controller;

use Mawufemor\Techandfun\helpers\SlugHelper;
use Mawufemor\Techandfun\model\Post;
use Mawufemor\Techandfun\model\Category;

if (!defined('ROOT')) {
    die("Direct access not allowed");
}

use PDO;

class PostController
{
    private Post $postModel;
    private Category $categoryModel;

    public function __construct(private PDO $pdo)
    {
        $this->postModel = new Post($this->pdo);
        $this->categoryModel = new Category($this->pdo);
    }

    public function getPostModel(): Post
    {
        return $this->postModel;
    }

    public function index(): void
    {
        $posts = $this->postModel->getAllPosts();
        require ROOT . '/app/view/public/post/index.php';
    }

    public function create(): void
    {

        requireLogin();
        requireRole('admin');
        $categories = $this->categoryModel->getAll();
        require ROOT . '/app/view/admin/post/create.php';
    }

    public function store(): void
    {
        requireLogin();
        requireRole('admin');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        // Validate CSRF token
        validateCSRF($_POST['csrf_token'] ?? '');

        $title       = trim($_POST['post_title'] ?? '');
        $content     = trim($_POST['body'] ?? '');
        $user_id     = $_SESSION['user_id'] ?? null;
        $category_id = $_POST['category_id'] ?? '';

        // Basic required-field validation
        if (empty($title) || empty($content) || empty($category_id)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        // category_id must be a valid positive integer
        if (!ctype_digit((string)$category_id)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid category selected.']);
            exit;
        }
        $category_id = (int) $category_id;

        // Confirm the category actually exists
        if (!$this->categoryModel->exists($category_id)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Selected category does not exist.']);
            exit;
        }

        // Generate a unique slug (handles collisions)
        $baseSlug = SlugHelper::generate($title);
        $slug     = $baseSlug;
        $suffix   = 1;

        while ($this->postModel->slugExists($slug)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        try {
            if ($this->postModel->createPost($title, $slug, $content, $user_id, $category_id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Post "' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" created successfully',
                    'slug'    => $slug,
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create post.']);
            }
        } catch (\PDOException $e) {
            error_log('Post creation failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
        }

        exit;
    }
    public function show(int $id){
    

}
}
