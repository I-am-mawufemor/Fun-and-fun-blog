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
        $activeCategory = $_GET['category'] ?? '';
        $categories = $this->categoryModel->getAll();
        $posts = $this->postModel->getPublishedPosts(); // new method, WHERE status = 'published'

        if (isLoggedIn() && isAdmin()) {
            $getAllPosts = $this->postModel->getAllPosts();
            require ROOT . '/app/view/admin/post/index.php';
            return;
        }
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

        $status = $_POST['status'] ?? 'draft';


        // Basic required-field validation
        if (empty($title) || empty($content) || empty($category_id)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }


        // Enforce the same length limits as the client-side maxlength attributes
        if (mb_strlen($title) > 255) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Title is too long.']);
            exit;
        }

        if (mb_strlen($content) > 20000) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Body exceeds the 20,000 character limit.']);
            exit;
        }

        $allowedStatuses = ['draft', 'published', 'archived'];
        if (!in_array($status, $allowedStatuses, true)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid status.']);
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
            if ($this->postModel->createPost($title, $slug, $content, $user_id, $category_id, $status)) {
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


    public function show(string $slug): void
    {
        $post = $this->postModel->getPostBySlug($slug);

        if (!$post) {
            http_response_code(404);
            require ROOT . '/app/view/errors/404.php';
            exit;
        }

        // Only published posts are visible to the public.
        // Logged-in admins can still preview drafts/archived posts.
        $isAdmin = isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin';

        if ($post['status'] !== 'published' && !$isAdmin) {
            http_response_code(404);
            require ROOT . '/app/view/errors/404.php';
            exit;
        }

        require ROOT . '/app/view/public/post/show.php';
    }

    public function updateStatus(): void
    {
        requireLogin();
        requireRole('admin');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        validateCSRF($_POST['csrf_token'] ?? '');

        $id     = $_POST['id'] ?? '';
        $status = $_POST['status'] ?? '';

        if (!ctype_digit((string)$id)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid post ID.']);
            exit;
        }

        $allowedStatuses = ['draft', 'published', 'archived'];
        if (!in_array($status, $allowedStatuses, true)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid status.']);
            exit;
        }

        if ($this->postModel->updateStatus((int)$id, $status)) {
            echo json_encode(['success' => true, 'message' => 'Post status updated to ' . $status . '.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
        }
        exit;
    }

    // show update page
    public function edit(int $id): void
    {
        requireLogin();
        requireRole('admin');

        $post = $this->postModel->getPostById($id);

        if (!$post) {
            $_SESSION['error'] = "Post not found.";
            header("Location: ?page=posts");
            exit;
        }

        $categories = $this->categoryModel->getAll();
        require ROOT . '/app/view/admin/post/edit.php';
    }


    // update post
    public function update(int $id): void
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

        // Confirm the post actually exists before doing anything else
        $post = $this->postModel->getPostById($id);
        if (!$post) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Post not found.']);
            exit;
        }

        $title       = trim($_POST['post_title'] ?? '');
        $content     = trim($_POST['body'] ?? '');
        $category_id = $_POST['category_id'] ?? '';
        $status      = $_POST['status'] ?? 'draft';

        // Basic required-field validation
        if (empty($title) || empty($content) || empty($category_id)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        // Enforce the same length limits as the client-side maxlength attributes
        if (mb_strlen($title) > 255) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Title is too long.']);
            exit;
        }

        if (mb_strlen($content) > 20000) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Body exceeds the 20,000 character limit.']);
            exit;
        }

        $allowedStatuses = ['draft', 'published', 'archived'];
        if (!in_array($status, $allowedStatuses, true)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid status.']);
            exit;
        }

        // category_id must be a valid positive integer
        if (!ctype_digit((string) $category_id)) {
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

        // Only regenerate the slug if the title actually changed, so existing links to the post remain valid. Otherwise, keep the existing slug.

        $slug = ($title !== $post['title'])
            ? SlugHelper::generate($title)
            : $post['slug'];

        try {
            if ($this->postModel->updatePost($id, $title, $slug, $content, $category_id, $status)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Post "' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" updated successfully',
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update post.']);
            }
        } catch (\PDOException $e) {
            error_log('Post update failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
        }

        exit;
    }
}
